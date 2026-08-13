<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('school_grading_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('grading_scheme_id')->constrained('grading_schemes')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id'], 'school_year_grading_unique');
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('grading_scheme_id')->constrained('grading_schemes')->restrictOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['school_id', 'grading_scheme_id']);
        });

        Schema::create('grade_scale_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_scale_id')->constrained('grade_scales')->cascadeOnDelete();
            $table->string('label');
            $table->string('code')->nullable();
            $table->decimal('min_score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->decimal('gpa_points', 4, 2)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_learning_area')->default(false);
            $table->foreignId('parent_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'name']);
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['school_id', 'class_id', 'subject_id', 'academic_year_id', 'term_id'],
                'class_subject_term_unique'
            );
        });

        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_competency')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'slug']);
        });

        Schema::create('assessment_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('grading_scheme_id')->constrained('grading_schemes')->restrictOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained('assessment_types')->cascadeOnDelete();
            $table->decimal('weight_percent', 5, 2);
            $table->timestamps();

            $table->unique(
                ['school_id', 'class_id', 'subject_id', 'term_id', 'assessment_type_id'],
                'assessment_weight_unique'
            );
        });

        Schema::create('teacher_subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'user_id']);
            $table->index(['school_id', 'class_id', 'subject_id']);
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained('assessment_types')->cascadeOnDelete();
            $table->string('title');
            $table->date('assessed_on')->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['school_id', 'term_id', 'class_id', 'subject_id']);
        });

        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->decimal('raw_score', 8, 2);
            $table->timestamps();

            $table->unique(['assessment_id', 'student_enrollment_id'], 'assessment_score_unique');
        });

        Schema::create('competency_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->string('band_code', 10);
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'student_enrollment_id'], 'competency_rating_unique');
        });

        Schema::create('subject_term_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->decimal('total_percent', 8, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->string('band_code')->nullable();
            $table->decimal('gpa_points', 4, 2)->nullable();
            $table->unsignedInteger('class_rank')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['student_enrollment_id', 'subject_id', 'term_id'],
                'subject_term_result_unique'
            );
        });

        Schema::create('term_report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pdf_path')->nullable();
            $table->json('scheme_snapshot')->nullable();
            $table->decimal('term_mean', 8, 2)->nullable();
            $table->unsignedInteger('term_rank')->nullable();
            $table->timestamps();

            $table->unique(['student_enrollment_id', 'term_id'], 'term_report_card_unique');
        });

        Schema::create('report_card_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_report_card_id')->constrained('term_report_cards')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('subject_name');
            $table->decimal('total_percent', 8, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->string('band_code')->nullable();
            $table->decimal('gpa_points', 4, 2)->nullable();
            $table->text('teacher_comment')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_items');
        Schema::dropIfExists('term_report_cards');
        Schema::dropIfExists('subject_term_results');
        Schema::dropIfExists('competency_ratings');
        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('teacher_subject_assignments');
        Schema::dropIfExists('assessment_weights');
        Schema::dropIfExists('assessment_types');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('grade_scale_bands');
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('school_grading_settings');
        Schema::dropIfExists('grading_schemes');
    }
};
