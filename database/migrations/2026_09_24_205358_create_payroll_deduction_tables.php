<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paye_bands', function (Blueprint $table) {
            $table->id();
            $table->date('effective_from');
            $table->unsignedTinyInteger('band_order');
            $table->decimal('lower_limit', 15, 2);
            $table->decimal('upper_limit', 15, 2)->nullable();
            $table->decimal('rate', 5, 2);
            $table->timestamps();

            $table->unique(['effective_from', 'band_order'], 'paye_bands_effective_order_unique');
        });

        Schema::create('statutory_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->date('effective_from');
            $table->decimal('employee_rate', 8, 4)->nullable();
            $table->decimal('employer_rate', 8, 4)->nullable();
            $table->decimal('fixed_amount', 15, 2)->nullable();
            $table->decimal('minimum_amount', 15, 2)->nullable();
            $table->decimal('lower_limit', 15, 2)->nullable();
            $table->decimal('upper_limit', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['code', 'effective_from'], 'statutory_rates_code_effective_unique');
        });

        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('kind');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_remaining', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'employee_id'], 'employee_deductions_school_employee_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('statutory_rates');
        Schema::dropIfExists('paye_bands');
    }
};