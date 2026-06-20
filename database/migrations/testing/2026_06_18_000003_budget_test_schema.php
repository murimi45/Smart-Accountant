<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'expense_category_id', 'term_id'], 'budgets_school_category_term_unique');
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->date('expense_date')->nullable();
            $table->foreignId('term_id')->constrained('terms');
            $table->year('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('budgets');
    }
};
