<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('term_id')->constrained('terms');

            $table->enum('status', [
                'active',
                'repeating',
                'inactive',
                'wrongly_promoted',
                'cancelled',
            ])->default('active');

            $table->foreignId('promoted_from_enrollment_id')
                  ->nullable()
                  ->constrained('student_enrollments')
                  ->nullOnDelete();

            $table->string('correction_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};