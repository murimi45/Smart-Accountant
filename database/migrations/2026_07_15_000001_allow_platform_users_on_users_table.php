<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Platform operators need school_id = null and role = platform.
 * Prod users.school_id was NOT NULL + FK; role was a narrow enum.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->nullOnDelete();
        });

        // Allow platform (and future roles) without fighting MySQL enum ALTER quirks.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('admin')->change();
        });
    }

    public function down(): void
    {
        // Cannot safely restore NOT NULL school_id while platform users exist.
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
        });

        DB::table('users')->whereNull('school_id')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')
                ->references('id')
                ->on('schools');
        });

        // Narrow role back only for known values that fit the old enum.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'accountant'])->default('admin')->change();
        });
    }
};
