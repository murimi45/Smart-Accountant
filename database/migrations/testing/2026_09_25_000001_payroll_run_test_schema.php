<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->date('period');
            $table->string('status')->default('draft');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'period'], 'payroll_runs_school_period_unique');
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('staff_number');
            $table->string('full_name');
            $table->boolean('included')->default(true);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('gross', 15, 2)->default(0);
            $table->decimal('nssf_employee', 15, 2)->default(0);
            $table->decimal('nssf_employer', 15, 2)->default(0);
            $table->decimal('shif', 15, 2)->default(0);
            $table->decimal('housing_employee', 15, 2)->default(0);
            $table->decimal('housing_employer', 15, 2)->default(0);
            $table->decimal('taxable_pay', 15, 2)->default(0);
            $table->decimal('paye', 15, 2)->default(0);
            $table->decimal('school_deductions_total', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2)->default(0);
            $table->decimal('employer_cost', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id'], 'payslips_run_employee_unique');
        });

        Schema::create('payslip_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();
            $table->foreignId('employee_deduction_id')->nullable()->constrained('employee_deductions')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('side');
            $table->decimal('amount', 15, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_lines');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_runs');
    }
};
