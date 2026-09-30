<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('basic_pay', 15, 2);
            $table->timestamps();

            $table->unique(['school_id', 'name'], 'salary_grades_school_name_unique');
        });

        Schema::create('salary_grade_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('salary_grade_id')->constrained('salary_grades')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->unique(
                ['salary_grade_id', 'name'],
                'salary_grade_allowances_grade_name_unique'
            );
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('staff_number');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->date('start_date')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('shif_number')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignId('salary_grade_id')->nullable()->constrained('salary_grades');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'staff_number'], 'employees_school_staff_number_unique');
            $table->index(['school_id', 'status'], 'employees_school_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('salary_grade_allowances');
        Schema::dropIfExists('salary_grades');
    }
};