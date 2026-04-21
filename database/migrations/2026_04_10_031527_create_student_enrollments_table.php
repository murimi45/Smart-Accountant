<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
          $table->id();

          $table->foreignId('student_id')->constrained()->cascadeOnDelete();
          $table->foreignId('class_id')->constrained('classes');
          $table->foreignId('stream_id')->nullable()->constrained('streams');

          $table->foreignId('term_id')->constrained('terms');
          $table->unsignedInteger('academic_year');

          $table->boolean('active')->default(true);

          $table->timestamps();

           $table->unique(['student_id','academic_year']);
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
