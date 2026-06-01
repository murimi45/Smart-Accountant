<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained();
            $table->foreignId('enrollment_id')
                  ->nullable()
                  ->constrained('student_enrollments')
                  ->nullOnDelete();

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid',  10, 2)->default(0);
            $table->decimal('balance',      10, 2)->default(0);
            $table->decimal('balance_forward', 10, 2)->default(0);
            $table->decimal('credit_forward',  10, 2)->default(0);
            $table->decimal('base_fee',        10, 2)->default(0);

            $table->date('invoice_date')->default(now());

            $table->enum('status', [
                'unpaid',
                'partially_paid',
                'paid',
                'voided',
            ])->default('unpaid');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};