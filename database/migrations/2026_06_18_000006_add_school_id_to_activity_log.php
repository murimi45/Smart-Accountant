<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->index(['school_id', 'log_name', 'created_at'], 'activity_log_school_log_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('activity_log_school_log_created_index');
            $table->dropColumn('school_id');
        });
    }
};
