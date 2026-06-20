<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->date('deposit_date');
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['unmatched', 'partial', 'reconciled'])->default('unmatched');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('bank_reconciliation_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->foreignId('bank_deposit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashbook_entry_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->foreignId('matched_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('matched_at');
            $table->timestamps();

            $table->unique('cashbook_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_matches');
        Schema::dropIfExists('bank_deposits');
    }
};
