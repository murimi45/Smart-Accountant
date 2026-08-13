<?php

namespace Tests\Feature\Grading;

use App\Core\Modules\ModuleRegistry;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\ClassSubject;
use App\Models\CompetencyRating;
use App\Models\GradingScheme;
use App\Models\Module;
use App\Models\SchoolGradingSetting;
use App\Models\Subject;
use App\Models\TeacherSubjectAssignment;
use App\Models\TermReportCard;
use App\Models\User;
use App\Modules\Grading\Services\MarkEntryService;
use App\Modules\Grading\Services\ReportCardPublisher;
use App\Modules\Grading\Services\ResultsEngine;
use App\Modules\Grading\Services\SchemeResolver;
use Database\Seeders\GradingSchemesSeeder;
use Illuminate\Support\Facades\Storage;
use Tests\Support\TenantFixtureBuilder;
use Tests\TestCase;

class GradingModuleTest extends TestCase
{
    private array $fixtures;

    private GradingScheme $cbc;

    private GradingScheme $international;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = TenantFixtureBuilder::createPair();
        (new GradingSchemesSeeder)->run();
        $this->cbc = GradingScheme::query()->where('slug', GradingScheme::SLUG_CBC)->firstOrFail();
        $this->international = GradingScheme::query()->where('slug', GradingScheme::SLUG_INTERNATIONAL)->firstOrFail();

        ModuleRegistry::enableForSchool((int) $this->fixtures['schoolA']->id, Module::SLUG_GRADING);
        ModuleRegistry::enableForSchool((int) $this->fixtures['schoolB']->id, Module::SLUG_GRADING);

        Storage::fake('local');
    }

    private function teacherA(): User
    {
        return User::unguarded(fn () => User::create([
            'school_id' => $this->fixtures['schoolA']->id,
            'admin_name' => 'Teacher A',
            'email' => 'teacher-a-grading@probe.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'two_factor_enabled' => false,
        ]));
    }

    private function configureInternational(): array
    {
        $tenant = $this->fixtures['tenantA'];
        $year = $tenant['year'];
        $term = $tenant['term'];
        $class = $tenant['class'];

        $this->actingAs($this->fixtures['adminA']);

        SchoolGradingSetting::createForSchool((int) $this->fixtures['schoolA']->id, [
            'academic_year_id' => $year->id,
            'grading_scheme_id' => $this->international->id,
        ]);

        app(SchemeResolver::class)->ensureDefaultScale((int) $this->fixtures['schoolA']->id, $this->international);

        $subject = Subject::createForSchool((int) $this->fixtures['schoolA']->id, [
            'name' => 'Mathematics',
            'code' => 'MATH',
        ]);

        ClassSubject::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'term_id' => $term->id,
        ]);

        $type = AssessmentType::createForSchool((int) $this->fixtures['schoolA']->id, [
            'name' => 'End Term',
            'slug' => 'end-term',
            'is_competency' => false,
        ]);

        AssessmentWeight::createForSchool((int) $this->fixtures['schoolA']->id, [
            'grading_scheme_id' => $this->international->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'assessment_type_id' => $type->id,
            'weight_percent' => 100,
        ]);

        return compact('subject', 'type', 'term', 'class', 'year');
    }

    private function configureCbc(): array
    {
        $tenant = $this->fixtures['tenantA'];
        $year = $tenant['year'];
        $term = $tenant['term'];
        $class = $tenant['class'];

        $this->actingAs($this->fixtures['adminA']);

        SchoolGradingSetting::createForSchool((int) $this->fixtures['schoolA']->id, [
            'academic_year_id' => $year->id,
            'grading_scheme_id' => $this->cbc->id,
        ]);

        app(SchemeResolver::class)->ensureDefaultScale((int) $this->fixtures['schoolA']->id, $this->cbc);

        $subject = Subject::createForSchool((int) $this->fixtures['schoolA']->id, [
            'name' => 'Literacy',
            'code' => 'LIT',
            'is_learning_area' => true,
        ]);

        ClassSubject::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'term_id' => $term->id,
        ]);

        $type = AssessmentType::createForSchool((int) $this->fixtures['schoolA']->id, [
            'name' => 'Formative',
            'slug' => 'formative',
            'is_competency' => true,
        ]);

        return compact('subject', 'type', 'term', 'class', 'year');
    }

    public function test_cross_tenant_subject_edit_is_blocked(): void
    {
        $this->actingAs($this->fixtures['adminB']);
        $subjectB = Subject::createForSchool((int) $this->fixtures['schoolB']->id, [
            'name' => 'Science B',
            'code' => 'SCI-B',
        ]);

        $response = $this->actingAs($this->fixtures['adminA'])
            ->put(route('grading.subjects.update', $subjectB->id), [
                'name' => 'Hacked',
                'code' => 'SCI-B',
            ]);

        $this->assertBlockedCrossTenant($response);
        $this->assertSame('Science B', $subjectB->fresh()->name);
    }

    public function test_teacher_cannot_enter_marks_for_unassigned_class_subject(): void
    {
        $cfg = $this->configureInternational();
        $teacher = $this->teacherA();

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Exam',
            'max_score' => 100,
            'status' => Assessment::STATUS_OPEN,
        ]);

        $this->actingAs($teacher)
            ->get(route('grading.mark-entry.show', $assessment))
            ->assertForbidden();
    }

    public function test_international_engine_computes_percent_and_letter(): void
    {
        $cfg = $this->configureInternational();
        $enrollment = $this->fixtures['tenantA']['enrollment'];

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Exam',
            'max_score' => 100,
            'status' => Assessment::STATUS_OPEN,
        ]);

        AssessmentScore::createForSchool((int) $this->fixtures['schoolA']->id, [
            'assessment_id' => $assessment->id,
            'student_enrollment_id' => $enrollment->id,
            'raw_score' => 85,
        ]);

        $result = app(ResultsEngine::class)->computeForEnrollment(
            (int) $this->fixtures['schoolA']->id,
            (int) $enrollment->id,
            (int) $cfg['term']->id,
            (int) $cfg['subject']->id
        );

        $this->assertEquals(85.0, (float) $result->total_percent);
        $this->assertEquals('A', $result->letter_grade);
        $this->assertEquals(4.0, (float) $result->gpa_points);
    }

    public function test_cbc_competency_entry_and_majority_band(): void
    {
        $cfg = $this->configureCbc();
        $enrollment = $this->fixtures['tenantA']['enrollment'];

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Observation',
            'status' => Assessment::STATUS_OPEN,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        app(MarkEntryService::class)->upsertCompetency($assessment, (int) $enrollment->id, 'XX');
    }

    public function test_cbc_engine_majority_band(): void
    {
        $cfg = $this->configureCbc();
        $enrollment = $this->fixtures['tenantA']['enrollment'];

        foreach (['ME', 'ME', 'AE'] as $i => $band) {
            $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
                'class_id' => $cfg['class']->id,
                'subject_id' => $cfg['subject']->id,
                'term_id' => $cfg['term']->id,
                'assessment_type_id' => $cfg['type']->id,
                'title' => 'Obs '.$i,
                'status' => Assessment::STATUS_CLOSED,
            ]);
            CompetencyRating::createForSchool((int) $this->fixtures['schoolA']->id, [
                'assessment_id' => $assessment->id,
                'student_enrollment_id' => $enrollment->id,
                'band_code' => $band,
            ]);
        }

        $result = app(ResultsEngine::class)->computeForEnrollment(
            (int) $this->fixtures['schoolA']->id,
            (int) $enrollment->id,
            (int) $cfg['term']->id,
            (int) $cfg['subject']->id
        );

        $this->assertEquals('ME', $result->band_code);
    }

    public function test_closed_assessment_rejects_mark_entry(): void
    {
        $cfg = $this->configureInternational();
        $enrollment = $this->fixtures['tenantA']['enrollment'];

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Closed exam',
            'max_score' => 50,
            'status' => Assessment::STATUS_CLOSED,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        app(MarkEntryService::class)->upsertScore($assessment, (int) $enrollment->id, 40);
    }

    public function test_publish_creates_pdf_and_freezes_items(): void
    {
        $cfg = $this->configureInternational();
        $enrollment = $this->fixtures['tenantA']['enrollment'];

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Exam',
            'max_score' => 100,
            'status' => Assessment::STATUS_CLOSED,
        ]);

        AssessmentScore::createForSchool((int) $this->fixtures['schoolA']->id, [
            'assessment_id' => $assessment->id,
            'student_enrollment_id' => $enrollment->id,
            'raw_score' => 72,
        ]);

        $card = TermReportCard::createForSchool((int) $this->fixtures['schoolA']->id, [
            'student_enrollment_id' => $enrollment->id,
            'term_id' => $cfg['term']->id,
            'status' => TermReportCard::STATUS_DRAFT,
        ]);

        $published = app(ReportCardPublisher::class)->publish($card, $this->fixtures['adminA']);

        $this->assertTrue($published->isPublished());
        $this->assertNotEmpty($published->pdf_path);
        Storage::disk('local')->assertExists($published->pdf_path);
        $this->assertCount(1, $published->items);
        $this->assertEquals('B', $published->items->first()->letter_grade);

        AssessmentScore::withoutGlobalScopes()
            ->where('assessment_id', $assessment->id)
            ->update(['raw_score' => 99]);

        $this->assertEquals('B', $published->fresh()->items->first()->letter_grade);
    }

    public function test_teacher_with_assignment_can_open_mark_entry(): void
    {
        $cfg = $this->configureInternational();
        $teacher = $this->teacherA();

        TeacherSubjectAssignment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'user_id' => $teacher->id,
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'academic_year_id' => $cfg['year']->id,
            'term_id' => $cfg['term']->id,
        ]);

        $assessment = Assessment::createForSchool((int) $this->fixtures['schoolA']->id, [
            'class_id' => $cfg['class']->id,
            'subject_id' => $cfg['subject']->id,
            'term_id' => $cfg['term']->id,
            'assessment_type_id' => $cfg['type']->id,
            'title' => 'Exam',
            'max_score' => 100,
            'status' => Assessment::STATUS_OPEN,
        ]);

        $this->actingAs($teacher)
            ->get(route('grading.mark-entry.show', $assessment))
            ->assertOk();
    }
}
