<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->indexExists('class_fees', 'class_fees_school_class_term_unique')) {
            $this->assertNoDuplicates('class_fees', ['school_id', 'class_id', 'term_id'], 'class fee');
            Schema::table('class_fees', function (Blueprint $table) {
                $table->unique(
                    ['school_id', 'class_id', 'term_id'],
                    'class_fees_school_class_term_unique'
                );
            });
        }

        if (! $this->indexExists('invoices', 'invoices_school_enrollment_unique')) {
            $this->assertNoDuplicates('invoices', ['school_id', 'enrollment_id'], 'invoice enrollment', true);
            Schema::table('invoices', function (Blueprint $table) {
                $table->unique(
                    ['school_id', 'enrollment_id'],
                    'invoices_school_enrollment_unique'
                );
            });
        }

        if (! $this->indexExists('income_categories', 'income_categories_school_name_unique')) {
            $this->assertNoDuplicates('income_categories', ['school_id', 'name'], 'income category');
            Schema::table('income_categories', function (Blueprint $table) {
                $table->unique(
                    ['school_id', 'name'],
                    'income_categories_school_name_unique'
                );
            });
        }

        if (! $this->indexExists('expense_categories', 'expense_categories_school_name_unique')) {
            $this->assertNoDuplicates('expense_categories', ['school_id', 'name'], 'expense category');
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->unique(
                    ['school_id', 'name'],
                    'expense_categories_school_name_unique'
                );
            });
        }

        // terms: keep existing (academic_year_id, term_number) — academic years are already per-school.
        // Replacing that index requires dropping FK-backed indexes on MySQL.

        if (! $this->indexExists('transactions', 'transactions_reference_unique')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unique('reference', 'transactions_reference_unique');
            });
        }

        if (! $this->indexExists('invoices', 'invoices_school_term_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['school_id', 'term_id'], 'invoices_school_term_index');
            });
        }

        if (! $this->indexExists('invoices', 'invoices_school_student_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['school_id', 'student_id'], 'invoices_school_student_index');
            });
        }

        if (! $this->indexExists('student_enrollments', 'enrollments_school_term_index')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->index(['school_id', 'term_id'], 'enrollments_school_term_index');
            });
        }

        if (! $this->indexExists('student_enrollments', 'enrollments_school_student_index')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->index(['school_id', 'student_id'], 'enrollments_school_student_index');
            });
        }

        if (! $this->indexExists('sms_logs', 'sms_logs_school_status_index')) {
            Schema::table('sms_logs', function (Blueprint $table) {
                $table->index(['school_id', 'status'], 'sms_logs_school_status_index');
            });
        }

        if (! $this->indexExists('expenses', 'expenses_school_term_index')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index(['school_id', 'term_id'], 'expenses_school_term_index');
            });
        }

        if (! $this->indexExists('extra_fees', 'extra_fees_school_term_index')) {
            Schema::table('extra_fees', function (Blueprint $table) {
                $table->index(['school_id', 'term_id'], 'extra_fees_school_term_index');
            });
        }

        if (! $this->indexExists('terms', 'terms_school_year_index')) {
            Schema::table('terms', function (Blueprint $table) {
                $table->index(['school_id', 'academic_year_id'], 'terms_school_year_index');
            });
        }
    }

    public function down(): void
    {
        $this->dropIndexIfExists('terms', 'terms_school_year_index');
        $this->dropIndexIfExists('extra_fees', 'extra_fees_school_term_index');
        $this->dropIndexIfExists('expenses', 'expenses_school_term_index');
        $this->dropIndexIfExists('sms_logs', 'sms_logs_school_status_index');
        $this->dropIndexIfExists('student_enrollments', 'enrollments_school_student_index');
        $this->dropIndexIfExists('student_enrollments', 'enrollments_school_term_index');
        $this->dropIndexIfExists('invoices', 'invoices_school_student_index');
        $this->dropIndexIfExists('invoices', 'invoices_school_term_index');
        $this->dropIndexIfExists('transactions', 'transactions_reference_unique');
        $this->dropIndexIfExists('expense_categories', 'expense_categories_school_name_unique');
        $this->dropIndexIfExists('income_categories', 'income_categories_school_name_unique');
        $this->dropIndexIfExists('invoices', 'invoices_school_enrollment_unique');
        $this->dropIndexIfExists('class_fees', 'class_fees_school_class_term_unique');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            $blueprint->dropIndex($indexName);
        });
    }

    /**
     * @param  list<string>  $columns
     */
    private function assertNoDuplicates(
        string $table,
        array $columns,
        string $label,
        bool $ignoreNullEnrollment = false
    ): void {
        $query = DB::table($table)
            ->select(array_merge($columns, [DB::raw('COUNT(*) as total')]))
            ->groupBy($columns)
            ->having('total', '>', 1);

        if ($ignoreNullEnrollment) {
            $query->whereNotNull('enrollment_id');
        }

        if ($query->get()->isNotEmpty()) {
            throw new RuntimeException(
                "Cannot add unique constraint for {$label}: duplicate rows exist in {$table}."
            );
        }
    }
};
