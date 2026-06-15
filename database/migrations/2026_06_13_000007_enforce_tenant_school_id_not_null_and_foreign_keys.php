<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->backfillSmsLogs();
        $this->backfillExpenseCategories();
        $this->backfillStudentHistories();
        $this->backfillTransactions();

        $this->enforceNotNull('sms_logs', 'school_id');
        $this->enforceNotNull('expense_categories', 'school_id');
        $this->enforceNotNull('student_histories', 'school_id');
        $this->enforceNotNull('transactions', 'school_id');

        $this->addForeignKeyIfMissing('expenses', 'school_id', 'expenses_school_id_foreign');
        $this->addForeignKeyIfMissing('cashbook_entries', 'school_id', 'cashbook_entries_school_id_foreign');
        $this->addForeignKeyIfMissing('income_categories', 'school_id', 'income_categories_school_id_foreign');
        $this->addForeignKeyIfMissing('other_incomes', 'school_id', 'other_incomes_school_id_foreign');
    }

    public function down(): void
    {
        $this->dropForeignKeyIfExists('other_incomes', 'other_incomes_school_id_foreign');
        $this->dropForeignKeyIfExists('income_categories', 'income_categories_school_id_foreign');
        $this->dropForeignKeyIfExists('cashbook_entries', 'cashbook_entries_school_id_foreign');
        $this->dropForeignKeyIfExists('expenses', 'expenses_school_id_foreign');

        $this->enforceNullable('transactions', 'school_id');
        $this->enforceNullable('student_histories', 'school_id');
        $this->enforceNullable('expense_categories', 'school_id');
        $this->enforceNullable('sms_logs', 'school_id');
    }

    private function backfillSmsLogs(): void
    {
        DB::table('sms_logs')
            ->join('students', 'students.id', '=', 'sms_logs.student_id')
            ->whereNull('sms_logs.school_id')
            ->update(['sms_logs.school_id' => DB::raw('students.school_id')]);

        if (DB::table('sms_logs')->whereNull('school_id')->exists()) {
            DB::table('sms_logs')->whereNull('school_id')->delete();
        }
    }

    private function backfillExpenseCategories(): void
    {
        $categories = DB::table('expense_categories')->whereNull('school_id')->get();

        foreach ($categories as $category) {
            $schoolId = DB::table('expenses')
                ->where('expense_category_id', $category->id)
                ->value('school_id');

            if ($schoolId) {
                DB::table('expense_categories')
                    ->where('id', $category->id)
                    ->update(['school_id' => $schoolId]);
            }
        }

        DB::table('expense_categories')
            ->whereNull('school_id')
            ->whereNotIn('id', DB::table('expenses')->whereNotNull('expense_category_id')->pluck('expense_category_id'))
            ->delete();

        if (DB::table('expense_categories')->whereNull('school_id')->exists()) {
            throw new RuntimeException(
                'Cannot enforce NOT NULL on expense_categories.school_id: orphan categories remain. Assign a school or delete them.'
            );
        }
    }

    private function backfillStudentHistories(): void
    {
        DB::table('student_histories')
            ->join('students', 'students.id', '=', 'student_histories.student_id')
            ->whereNull('student_histories.school_id')
            ->update(['student_histories.school_id' => DB::raw('students.school_id')]);

        if (DB::table('student_histories')->whereNull('school_id')->exists()) {
            throw new RuntimeException(
                'Cannot enforce NOT NULL on student_histories.school_id: rows exist without a resolvable student school.'
            );
        }
    }

    private function backfillTransactions(): void
    {
        DB::table('transactions')
            ->join('students', 'students.id', '=', 'transactions.student_id')
            ->whereNull('transactions.school_id')
            ->update(['transactions.school_id' => DB::raw('students.school_id')]);

        if (DB::table('transactions')->whereNull('school_id')->exists()) {
            throw new RuntimeException(
                'Cannot enforce NOT NULL on transactions.school_id: rows exist without school_id. Backfill or delete them first.'
            );
        }
    }

    private function enforceNotNull(string $table, string $column): void
    {
        DB::statement(sprintf(
            'ALTER TABLE `%s` MODIFY `%s` BIGINT UNSIGNED NOT NULL',
            $table,
            $column
        ));
    }

    private function enforceNullable(string $table, string $column): void
    {
        DB::statement(sprintf(
            'ALTER TABLE `%s` MODIFY `%s` BIGINT UNSIGNED NULL',
            $table,
            $column
        ));
    }

    private function addForeignKeyIfMissing(string $table, string $column, string $constraintName): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        if ($exists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column, $constraintName) {
            $blueprint->foreign($column, $constraintName)
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();
        });
    }

    private function dropForeignKeyIfExists(string $table, string $constraintName): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        if (! $exists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($constraintName) {
            $blueprint->dropForeign($constraintName);
        });
    }
};
