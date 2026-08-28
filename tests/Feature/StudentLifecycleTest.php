<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentExtraFee;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class StudentLifecycleTest extends TestCase
{
    private array $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
    }

    public function test_has_any_invoice_payments_is_false_for_mistake_student_and_true_after_payment(): void
    {
        $student = $this->fixtures['tenantA']['student'];
        $invoice = $this->fixtures['tenantA']['invoice'];

        $this->assertFalse($student->hasAnyInvoicePayments());

        $this->recordPayment($invoice, 2500);

        $this->assertTrue($student->fresh()->hasAnyInvoicePayments());
    }

    public function test_inactive_with_no_payment_voids_the_invoice(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $enrollment = $tenant['enrollment'];
        $invoice = $tenant['invoice'];

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('enrollment.update-status', $enrollment->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('enrollment.index'))
            ->assertSessionHas('success', 'Student marked inactive. Their invoice for this term has been voided.');

        $this->assertSame(StudentEnrollment::STATUS_INACTIVE, $enrollment->fresh()->status);
        $this->assertSame(Invoice::STATUS_VOIDED, $invoice->fresh()->status);
    }

    public function test_inactive_with_partial_payment_keeps_invoice_and_balance(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $enrollment = $tenant['enrollment'];
        $invoice = $tenant['invoice'];

        $invoice->update([
            'amount_paid' => 4000,
            'balance'     => 6000,
            'status'      => Invoice::STATUS_PARTIALLY_PAID,
        ]);
        $this->recordPayment($invoice, 4000);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('enrollment.update-status', $enrollment->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('enrollment.index'))
            ->assertSessionHas(
                'success',
                'Student marked inactive. Their invoice was kept because payments exist; the remaining balance is still due.'
            );

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $invoice->status);
        $this->assertSame(4000.0, (float) $invoice->amount_paid);
        $this->assertSame(6000.0, (float) $invoice->balance);
    }

    public function test_inactive_when_fully_paid_leaves_invoice_paid(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $enrollment = $tenant['enrollment'];
        $invoice = $tenant['invoice'];

        $invoice->update([
            'amount_paid' => 10000,
            'balance'     => 0,
            'status'      => Invoice::STATUS_PAID,
        ]);
        $this->recordPayment($invoice, 10000);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('enrollment.update-status', $enrollment->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('enrollment.index'));

        $this->assertSame(Invoice::STATUS_PAID, $invoice->fresh()->status);
    }

    public function test_inactive_does_not_change_a_transferred_invoice(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $enrollment = $tenant['enrollment'];
        $invoice = $tenant['invoice'];

        $invoice->update([
            'status'  => Invoice::STATUS_TRANSFERRED,
            'balance' => 0,
            'notes'   => 'Balance carried forward.',
        ]);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('enrollment.update-status', $enrollment->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('enrollment.index'));

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_TRANSFERRED, $invoice->status);
        $this->assertSame('Balance carried forward.', $invoice->notes);
    }

    public function test_past_term_enrollment_cannot_be_set_inactive(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $enrollment = $tenant['enrollment'];

        $tenant['term']->update(['active' => false]);
        $tenant['toTerm']->update(['active' => true]);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('enrollment.index'))
            ->post(route('enrollment.update-status', $enrollment->id), [
                'status' => StudentEnrollment::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('enrollment.index'))
            ->assertSessionHas('error', 'Enrollment status can only be changed for the current term.');

        $this->assertSame(StudentEnrollment::STATUS_ACTIVE, $enrollment->fresh()->status);
        $this->assertSame(Invoice::STATUS_UNPAID, $tenant['invoice']->fresh()->status);
    }

    public function test_mistake_student_without_payments_is_hard_deleted(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $student = $tenant['student'];
        $studentId = $student->id;
        $admission = $student->admission;
        $invoiceId = $tenant['invoice']->id;
        $schoolId = (int) $this->fixtures['schoolA']->id;

        $this->assertFalse($student->hasAnyInvoicePayments());

        $tenant['invoice']->items()->create([
            'description' => 'Tuition',
            'amount'      => 10000,
        ]);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('listStudents'))
            ->get(route('deleteStudent', $studentId))
            ->assertRedirect(route('listStudents'))
            ->assertSessionHas('success');

        $this->assertNull(Student::withTrashed()->find($studentId));
        $this->assertFalse(
            StudentEnrollment::withoutGlobalScopes()->where('student_id', $studentId)->exists()
        );
        $this->assertFalse(
            Invoice::withoutGlobalScopes()->where('student_id', $studentId)->exists()
        );
        $this->assertFalse(
            InvoiceItem::withoutGlobalScopes()->where('invoice_id', $invoiceId)->exists()
        );
        $this->assertFalse(
            StudentExtraFee::withTrashed()->where('student_id', $studentId)->exists()
        );

        $replacement = Student::createForSchool($schoolId, [
            'full_name' => 'Replacement Student',
            'phone'     => '0711111111',
            'admission' => $admission,
            'gender'    => 'male',
        ]);

        $this->assertSame($admission, $replacement->admission);
    }

    public function test_delete_is_blocked_when_student_has_any_payment(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $student = $tenant['student'];

        $this->recordPayment($tenant['invoice'], 500);

        $this->actingAs($this->fixtures['adminA'])
            ->from(route('listStudents'))
            ->get(route('deleteStudent', $student->id))
            ->assertRedirect(route('listStudents'))
            ->assertSessionHas(
                'error',
                'This student has fee payment history and cannot be deleted. If they have left or transferred, mark them inactive on Enrollment instead.'
            );

        $this->assertNotNull(Student::find($student->id));
    }

    public function test_student_list_delete_uses_modal_instead_of_confirm(): void
    {
        $this->actingAs($this->fixtures['adminA'])
            ->get(route('listStudents'))
            ->assertOk()
            ->assertSee('deleteStudentModal')
            ->assertSee('This is transfer/leave')
            ->assertSee('Delete mistaken student')
            ->assertDontSee("Are you sure you want to delete this student?");
    }

    public function test_enrollment_status_dropdown_is_disabled_for_non_active_terms(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $admin = $this->fixtures['adminA'];

        $this->actingAs($admin)
            ->get(route('enrollment.index'))
            ->assertOk()
            ->assertSee('id="status-form-'.$tenant['enrollment']->id.'"', false)
            ->assertSee($tenant['student']->full_name);

        $tenant['term']->update(['active' => false]);
        $tenant['toTerm']->update(['active' => true]);

        $this->actingAs($admin)
            ->get(route('enrollment.index', ['term_id' => $tenant['term']->id]))
            ->assertOk()
            ->assertSee($tenant['student']->full_name)
            ->assertDontSee('id="status-form-'.$tenant['enrollment']->id.'"', false)
            ->assertSee('badge-status status-active');
    }

    public function test_enrollment_index_hides_soft_deleted_students(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $student = $tenant['student'];

        $student->delete();

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('enrollment.index'))
            ->assertOk()
            ->assertDontSee($student->full_name)
            ->assertDontSee($student->admission);
    }

    public function test_extra_fee_lists_hide_deleted_students(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $student = $tenant['student'];
        $extraFee = $tenant['extraFee'];

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('listextrafeestudents'))
            ->assertOk()
            ->assertSee($student->full_name);

        $student->delete();

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('listextrafeestudents'))
            ->assertOk()
            ->assertDontSee($student->full_name);

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('assignextrafeeform', ['extra_fee_id' => $extraFee->id]))
            ->assertOk()
            ->assertDontSee($student->full_name);
    }

    public function test_inactive_student_is_not_listed_for_extra_fee_assignment(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $student = $tenant['student'];
        $extraFee = $tenant['extraFee'];

        $tenant['enrollment']->update(['status' => StudentEnrollment::STATUS_INACTIVE]);

        $this->actingAs($this->fixtures['adminA'])
            ->get(route('assignextrafeeform', ['extra_fee_id' => $extraFee->id]))
            ->assertOk()
            ->assertDontSee($student->full_name);
    }

    public function test_dashboard_term_billed_keeps_transferred_and_excludes_voided(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $invoice = $tenant['invoice'];
        $admin = $this->fixtures['adminA'];
        $termId = $tenant['term']->id;

        $this->actingAs($admin)
            ->get(route('dashboard', ['view' => 'term', 'term_id' => $termId]))
            ->assertOk()
            ->assertSee('10,000.00');

        $invoice->update(['status' => Invoice::STATUS_TRANSFERRED]);

        $this->actingAs($admin)
            ->get(route('dashboard', ['view' => 'term', 'term_id' => $termId]))
            ->assertOk()
            ->assertSee('10,000.00');

        $invoice->update(['status' => Invoice::STATUS_VOIDED]);

        $this->actingAs($admin)
            ->get(route('dashboard', ['view' => 'term', 'term_id' => $termId]))
            ->assertOk()
            ->assertDontSee('10,000.00');
    }

    public function test_dashboard_annual_billed_excludes_voided_and_balance_forward(): void
    {
        $tenant = $this->fixtures['tenantA'];
        $admin = $this->fixtures['adminA'];
        $schoolId = (int) $this->fixtures['schoolA']->id;

        $tenant['invoice']->update([
            'status'       => Invoice::STATUS_TRANSFERRED,
            'total_amount' => 10000,
            'balance_forward' => 0,
        ]);

        Invoice::createForSchool($schoolId, [
            'student_id'      => $tenant['student']->id,
            'term_id'         => $tenant['toTerm']->id,
            'enrollment_id'   => $tenant['enrollment']->id,
            'total_amount'    => 8000,
            'amount_paid'     => 0,
            'balance'         => 8000,
            'balance_forward' => 3000,
            'credit_forward'  => 0,
            'invoice_date'    => now()->toDateString(),
            'status'          => Invoice::STATUS_UNPAID,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard', [
                'view' => 'annual',
                'academic_year_id' => $tenant['year']->id,
            ]))
            ->assertOk()
            ->assertSee('15,000.00');
    }

    private function recordPayment(Invoice $invoice, float $amount): void
    {
        InvoicePayment::create([
            'invoice_id'   => $invoice->id,
            'amount'       => $amount,
            'method'       => 'Cash',
            'payment_date' => now()->toDateString(),
        ]);
    }
}
