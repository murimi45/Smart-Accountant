<?php

namespace Tests\Support;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ExpenseCategory;
use App\Models\ExtraFee;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\PaymentChannel;
use App\Models\PromotionRun;
use App\Models\Schools;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentExtraFee;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Builds two isolated tenants with parallel structure (School A then School B).
 */
class TenantFixtureBuilder
{
    public static function createPair(): array
    {
        $schoolA = self::createSchool('A');
        $schoolB = self::createSchool('B');

        $adminA = self::createUser($schoolA, 'admin-a@test.local', 'admin');
        $adminB = self::createUser($schoolB, 'admin-b@test.local', 'admin');
        $accountantA = self::createUser($schoolA, 'acct-a@test.local', 'accountant');

        $tenantA = self::buildTenantGraph($schoolA, $adminA);
        $tenantB = self::buildTenantGraph($schoolB, $adminB);

        return [
            'schoolA' => $schoolA,
            'schoolB' => $schoolB,
            'adminA' => $adminA,
            'adminB' => $adminB,
            'accountantA' => $accountantA,
            'tenantA' => $tenantA,
            'tenantB' => $tenantB,
        ];
    }

    private static function createSchool(string $label): Schools
    {
        return Schools::create([
            'school_name'     => "Probe School {$label}",
            'email'           => "school-{$label}@probe.test",
            'phone'           => '0700000001',
            'subscription_status' => 'active',
        ]);
    }

    private static function createUser(Schools $school, string $email, string $role): User
    {
        return User::unguarded(function () use ($school, $email, $role) {
            return User::create([
                'school_id'          => $school->id,
                'admin_name'         => "User {$email}",
                'email'              => $email,
                'password'           => Hash::make('password'),
                'role'               => $role,
                'two_factor_enabled' => false,
            ]);
        });
    }

  /**
     * @return array{
     *   year: AcademicYear,
     *   term: Term,
     *   class: Classes,
     *   stream: Stream,
     *   student: Student,
     *   enrollment: StudentEnrollment,
     *   invoice: Invoice,
     *   extraFee: ExtraFee,
     *   assignment: StudentExtraFee,
     *   toTerm: Term,
     *   promotionRun: PromotionRun,
     *   expenseCategory: ExpenseCategory,
     *   incomeCategory: IncomeCategory,
     *   paymentChannel: PaymentChannel,
     *   staffUser: User,
     * }
     */
    private static function buildTenantGraph(Schools $school, User $actor): array
    {
        return Model::withoutEvents(function () use ($school, $actor) {
            $year = AcademicYear::createForSchool($school->id, [
                'name'       => '2026',
                'is_current' => true,
                'start_date' => '2026-01-01',
                'end_date'   => '2026-12-31',
            ]);

            $term = Term::createForSchool($school->id, [
                'name'             => 'Term 1',
                'term_number'      => 1,
                'academic_year_id' => $year->id,
                'start_date'       => '2026-01-01',
                'end_date'         => '2026-04-30',
                'active'           => true,
            ]);

            $class = Classes::createForSchool($school->id, [
                'name'  => 'Grade 1',
                'order' => 1,
            ]);

            $stream = Stream::create([
                'class_id' => $class->id,
                'name'     => 'A',
            ]);

            $student = Student::createForSchool($school->id, [
                'full_name' => "Student {$school->school_name}",
                'phone'     => '0711111111',
                'admission' => "ADM-{$school->id}",
                'gender'    => 'male',
            ]);

            $enrollment = StudentEnrollment::createForSchool($school->id, [
                'student_id' => $student->id,
                'class_id'   => $class->id,
                'stream_id'  => $stream->id,
                'term_id'    => $term->id,
                'status'     => StudentEnrollment::STATUS_ACTIVE,
            ]);

            $invoice = Invoice::createForSchool($school->id, [
                'student_id'    => $student->id,
                'term_id'       => $term->id,
                'enrollment_id' => $enrollment->id,
                'total_amount'  => 10000,
                'amount_paid'   => 0,
                'balance'       => 10000,
                'invoice_date'  => now()->toDateString(),
                'status'        => Invoice::STATUS_UNPAID,
            ]);

            $extraFee = ExtraFee::createForSchool($school->id, [
                'name'              => 'Transport',
                'amount'            => 500,
                'is_quantity_based' => false,
                'description'       => 'Bus',
                'status'            => 'active',
                'term_id'           => $term->id,
                'year'              => 2026,
                'created_by'        => $actor->id,
            ]);

            $assignment = StudentExtraFee::createForSchool($school->id, [
                'student_id'   => $student->id,
                'extra_fee_id' => $extraFee->id,
                'quantity'     => 1,
                'amount'       => 500,
                'created_by'   => $actor->id,
            ]);

            $toTerm = Term::createForSchool($school->id, [
                'name'             => 'Term 2',
                'term_number'      => 2,
                'academic_year_id' => $year->id,
                'start_date'       => '2026-05-01',
                'end_date'         => '2026-08-31',
                'active'           => false,
            ]);

            $promotionRun = PromotionRun::createForSchool($school->id, [
                'from_term_id' => $term->id,
                'to_term_id'   => $toTerm->id,
                'promoted_by'  => $actor->id,
                'status'       => 'pending',
                'type'         => 'term_promotion',
            ]);

            $expenseCategory = ExpenseCategory::createForSchool($school->id, [
                'name'        => 'Supplies',
                'description' => 'Office supplies',
            ]);

            $incomeCategory = IncomeCategory::createForSchool($school->id, [
                'name'        => 'Donations',
                'description' => 'General donations',
            ]);

            $paymentChannel = PaymentChannel::createForSchool($school->id, [
                'type'       => 'paybill',
                'identifier' => 'PB'.$school->id,
                'is_active'  => true,
            ]);

            $staffUser = User::unguarded(function () use ($school) {
                return User::create([
                    'school_id'          => $school->id,
                    'admin_name'         => "Staff {$school->id}",
                    'email'              => "staff-{$school->id}@probe.test",
                    'password'           => Hash::make('password'),
                    'role'               => 'admin',
                    'two_factor_enabled' => false,
                ]);
            });

            return compact(
                'year',
                'term',
                'toTerm',
                'class',
                'stream',
                'student',
                'enrollment',
                'invoice',
                'extraFee',
                'assignment',
                'promotionRun',
                'expenseCategory',
                'incomeCategory',
                'paymentChannel',
                'staffUser',
            );
        });
    }
}
