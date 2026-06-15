<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->foreignId('school_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::table('sms_logs')
            ->join('students', 'students.id', '=', 'sms_logs.student_id')
            ->whereNotNull('sms_logs.student_id')
            ->update(['sms_logs.school_id' => DB::raw('students.school_id')]);
    }

    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });
    }
};
