<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $dupes = DB::table('students')
            ->select('school_id', 'admission', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('school_id', 'admission')
            ->having('total', '>', 1)
            ->get();

        if ($dupes->isNotEmpty()) {
            throw new RuntimeException(
                'Cannot add unique (school_id, admission): duplicate admission numbers exist within a school. Resolve duplicates before migrating.'
            );
        }

        Schema::table('students', function (Blueprint $table) {
            $table->unique(['school_id', 'admission'], 'students_school_admission_unique');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_school_admission_unique');
        });
    }
};
