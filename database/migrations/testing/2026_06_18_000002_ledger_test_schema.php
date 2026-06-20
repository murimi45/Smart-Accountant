<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('category', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('cashbook_entry_id')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();

            $table->foreign('cashbook_entry_id')
                ->references('id')
                ->on('cashbook_entries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('accounts');
    }
};
