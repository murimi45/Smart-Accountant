<?php

namespace Tests\Support;

use App\Core\Modules\ModuleRegistry;
use App\Models\AcademicYear;
use App\Models\BankDeposit;
use App\Models\Classes;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExtraFee;
use App\Models\IncomeCategory;
use App\Models\Invoice;
use App\Models\InvoiceWaiver;
use App\Models\Module;
use App\Models\OtherIncome;
use App\Models\PaymentChannel;
use App\Models\PromotionRun;
use App\Models\Schools;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentExtraFee;
use App\Models\Term;
use App\Models\User;
use Database\Seeders\AccountsTableSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Builds two isolated tenants with parallel structure (School A then School B).
 */
class TenantFixtureBuilder
{
    public static function createPair(): array
    {
        self::ensureModuleCatalog();

        $schoolA = self::createSchool('A');
        $schoolB = self::createSchool('B');

        $adminA = self::createUser($schoolA, 'admin-a@test.local', 'admin');
        $adminB = self::createUser($schoolB, 'admin-b@test.local', 'admin');
        $accountantA = self::createUser($schoolA, 'acct-a@test.local', 'accountant');
        $accountantB = self::createUser($schoolB, 'acct-b@test.local', 'accountant');

        $tenantA = self::buildTenantGraph($schoolA, $adminA);
        $tenantB = self::buildTenantGraph($schoolB, $adminB, withPendingWaiver: true);

        return [
            'schoolA' => $schoolA,
            'schoolB' => $schoolB,
            'adminA' => $adminA,
            'adminB' => $adminB,
            'accountantA' => $accountantA,
            'accountantB' => $accountantB,
            'tenantA' => $tenantA,
            'tenantB' => $tenantB,
        ];
    }

    private static function ensureModuleCatalog(): void
    {
        if (Module::query()->where('slug', Module::SLUG_ACCOUNTANT)->exists()) {
            return;
        }

        foreach ([
            ['slug' => Module::SLUG_ACCOUNTANT, 'name' => 'Accountant', 'is_core' => true],
            ['slug' => Module::SLUG_HR, 'name' => 'HR', 'is_core' => false],
            ['slug' => Module::SLUG_GRADING, 'name' => 'Grading', 'is_core' => false],
        ] as $row) {
            Module::query()->create(array_merge([
                'description' => null,
                'version'     => '1.0.0',
            ], $row));
        }
    }

    private static function createSchool(string $label): Schools
    {
        $school = Schools::create([
            'school_name'     => "Probe School {$label}",
            'email'           => "school-{$label}@probe.test",
            'phone'           => '0700000001',
            'subscription_status' => 'active',
        ]);

        (new AccountsTableSeeder)->run($school->id);
        ModuleRegistry::enableForSchool((int) $school->id, Module::SLUG_ACCOUNTANT);

        return $school;
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
     *   invoiceWaiver?: InvoiceWaiver,
     *   extraFee: ExtraFee,
     *   assignment: StudentExtraFee,
     *   toTerm: Term,
     *   promotionRun: PromotionRun,
     *   expenseCategory: ExpenseCategory,
     *   expense: Expense,
     *   incomeCategory: IncomeCategory,
     *   otherIncome: OtherIncome,
     *   paymentChannel: PaymentChannel,
     *   bankDeposit: BankDeposit,
     *   staffUser: User,
     * }
     */
    private static function buildTenantGraph(Schools $school, User $actor, bool $withPendingWaiver = false): array
    {
        return Model::withoutEvents(function () use ($school, $actor, $withPendingWaiver) {
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

            $invoiceWaiver = null;
            if ($withPendingWaiver) {
                $invoiceWaiver = InvoiceWaiver::createForSchool($school->id, [
                    'invoice_id'     => $invoice->id,
                    'scope'          => InvoiceWaiver::SCOPE_INVOICE,
                    'discount_type'  => InvoiceWaiver::TYPE_FIXED,
                    'value'          => 500,
                    'reason'         => 'Pending waiver for isolation tests',
                    'status'         => InvoiceWaiver::STATUS_PENDING,
                    'requested_by'   => $actor->id,
                    'requested_at'   => now(),
                ]);
            }

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

            $expense = Expense::createForSchool($school->id, [
                'expense_category_id' => $expenseCategory->id,
                'description'         => 'Stationery',
                'amount'              => 1500,
                'payment_method'      => 'cash',
                'expense_date'        => $term->start_date,
                'term_id'             => $term->id,
                'year'                => 2026,
                'created_by'          => $actor->id,
            ]);

            $incomeCategory = IncomeCategory::createForSchool($school->id, [
                'name'        => 'Donations',
                'description' => 'General donations',
            ]);

            $otherIncome = OtherIncome::createForSchool($school->id, [
                'income_category_id' => $incomeCategory->id,
                'description'        => 'Parent contribution',
                'amount'             => 2500,
                'payment_method'     => 'cash',
                'income_date'        => $term->start_date,
                'term_id'            => $term->id,
                'year'               => 2026,
                'created_by'         => $actor->id,
            ]);

            $paymentChannel = PaymentChannel::createForSchool($school->id, [
                'type'       => 'paybill',
                'identifier' => 'PB'.$school->id,
                'is_active'  => true,
            ]);

            $bankDeposit = BankDeposit::createForSchool($school->id, [
                'deposit_date' => $term->start_date,
                'amount'       => 5000,
                'reference'    => 'DEP-'.$school->id,
                'description'  => 'Test deposit',
                'status'       => BankDeposit::STATUS_UNMATCHED,
                'recorded_by'  => $actor->id,
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
                'invoiceWaiver',
                'extraFee',
                'assignment',
                'promotionRun',
                'expenseCategory',
                'expense',
                'incomeCategory',
                'otherIncome',
                'paymentChannel',
                'bankDeposit',
                'staffUser',
            );
        });
    }
}
