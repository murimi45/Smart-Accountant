<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('imported_opening_balance', 10, 2)->default(0);
            $table->string('opening_balance_notes', 255)->nullable();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_opening_balance')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('is_opening_balance');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['imported_opening_balance', 'opening_balance_notes']);
        });
    }
};
