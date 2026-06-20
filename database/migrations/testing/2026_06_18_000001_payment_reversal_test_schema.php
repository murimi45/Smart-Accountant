<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payment_reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('reason', 500);
            $table->foreignId('reversed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('reversed_at');
            $table->timestamps();
        });

        Schema::create('cashbook_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->enum('transaction_type', ['inflow', 'outflow']);
            $table->string('entry_type')->default('original');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('related_entry_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->date('transaction_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['school_id']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('class_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_fees');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('cashbook_entries');
        Schema::dropIfExists('invoice_payment_reversals');
    }
};
