<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_fee_assignments', function (Blueprint $table) {

            // 1️⃣ Drop foreign keys first
            $table->dropForeign(['student_id']);
            $table->dropForeign(['extra_fee_id']);

            // 2️⃣ Drop old unique constraint
            $table->dropUnique('extra_fee_assignments_student_id_extra_fee_id_unique');

            // 3️⃣ Recreate foreign keys
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->cascadeOnDelete();

            $table->foreign('extra_fee_id')
                  ->references('id')
                  ->on('extra_fees')
                  ->cascadeOnDelete();

            // 4️⃣ Add new multi-tenant unique constraint
            $table->unique(
                ['student_id', 'extra_fee_id', 'school_id'],
                'efa_student_fee_school_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('extra_fee_assignments', function (Blueprint $table) {

            // 1️⃣ Drop new unique constraint
            $table->dropUnique('efa_student_fee_school_unique');

            // 2️⃣ Drop foreign keys again
            $table->dropForeign(['student_id']);
            $table->dropForeign(['extra_fee_id']);

            // 3️⃣ Restore old unique constraint
            $table->unique(
                ['student_id', 'extra_fee_id'],
                'extra_fee_assignments_student_id_extra_fee_id_unique'
            );

            // 4️⃣ Recreate foreign keys
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->cascadeOnDelete();

            $table->foreign('extra_fee_id')
                  ->references('id')
                  ->on('extra_fees')
                  ->cascadeOnDelete();
        });
    }
};
