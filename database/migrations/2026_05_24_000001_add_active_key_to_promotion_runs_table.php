<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('promotion_runs', 'active_key')) {
            Schema::table('promotion_runs', function (Blueprint $table) {
                $table->string('active_key', 64)->nullable()->after('type');
                $table->unique('active_key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('promotion_runs', 'active_key')) {
            Schema::table('promotion_runs', function (Blueprint $table) {
                $table->dropUnique(['active_key']);
                $table->dropColumn('active_key');
            });
        }
    }
};
