<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_histories', function (Blueprint $table) {
            $table->foreignId('school_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::table('student_histories')
            ->join('students', 'students.id', '=', 'student_histories.student_id')
            ->update(['student_histories.school_id' => DB::raw('students.school_id')]);

        Schema::table('student_histories', function (Blueprint $table) {
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_histories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });
    }
};
