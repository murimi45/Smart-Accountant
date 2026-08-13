<?php

namespace App\Modules\Grading\Services;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentWeight;
use App\Models\CompetencyRating;
use App\Models\GradeScale;
use App\Models\GradingScheme;
use App\Models\SubjectTermResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ResultsEngine
{
    public function __construct(
        private SchemeResolver $schemes,
        private RosterService $roster,
    ) {}

    public function computeForEnrollment(int $schoolId, int $enrollmentId, int $termId, int $subjectId, ?string $teacherComment = null): SubjectTermResult
    {
        $scheme = $this->schemes->schemeForTerm($schoolId, $termId);
        if (! $scheme) {
            throw new \RuntimeException('No grading scheme configured for this academic year.');
        }

        if ($scheme->isCbc()) {
            return $this->computeCbc($schoolId, $enrollmentId, $termId, $subjectId, $teacherComment);
        }

        return $this->computeInternational($schoolId, $enrollmentId, $termId, $subjectId, $scheme, $teacherComment);
    }

    public function computeClassSubject(int $schoolId, int $termId, int $classId, int $subjectId, ?int $streamId = null): Collection
    {
        $enrollments = $this->roster->enrollmentsFor($schoolId, $termId, $classId, $streamId);
        $results = collect();

        foreach ($enrollments as $enrollment) {
            $results->push($this->computeForEnrollment($schoolId, $enrollment->id, $termId, $subjectId));
        }

        $scheme = $this->schemes->schemeForTerm($schoolId, $termId);
        if ($scheme?->isInternational()) {
            $ranked = $results->sortByDesc(fn ($r) => (float) ($r->total_percent ?? 0))->values();
            $rank = 1;
            foreach ($ranked as $result) {
                $result->class_rank = $rank++;
                $result->save();
            }
        }

        return $results;
    }

    private function computeCbc(int $schoolId, int $enrollmentId, int $termId, int $subjectId, ?string $teacherComment): SubjectTermResult
    {
        $ratings = CompetencyRating::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('student_enrollment_id', $enrollmentId)
            ->whereHas('assessment', function ($q) use ($termId, $subjectId) {
                $q->withoutGlobalScopes()
                    ->where('term_id', $termId)
                    ->where('subject_id', $subjectId)
                    ->whereIn('status', [Assessment::STATUS_OPEN, Assessment::STATUS_CLOSED]);
            })
            ->pluck('band_code');

        $band = $this->majorityBand($ratings);

        return SubjectTermResult::withoutGlobalScopes()->updateOrCreate(
            [
                'student_enrollment_id' => $enrollmentId,
                'subject_id' => $subjectId,
                'term_id' => $termId,
            ],
            [
                'school_id' => $schoolId,
                'total_percent' => null,
                'letter_grade' => null,
                'band_code' => $band,
                'gpa_points' => null,
                'teacher_comment' => $teacherComment,
            ]
        );
    }

    private function computeInternational(
        int $schoolId,
        int $enrollmentId,
        int $termId,
        int $subjectId,
        GradingScheme $scheme,
        ?string $teacherComment
    ): SubjectTermResult {
        $enrollment = DB::table('student_enrollments')->where('id', $enrollmentId)->first();
        $classId = (int) $enrollment->class_id;

        $weights = AssessmentWeight::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->get()
            ->keyBy('assessment_type_id');

        $assessments = Assessment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('term_id', $termId)
            ->where('subject_id', $subjectId)
            ->where('class_id', $classId)
            ->whereIn('status', [Assessment::STATUS_OPEN, Assessment::STATUS_CLOSED])
            ->get();

        $byType = [];
        foreach ($assessments as $assessment) {
            $score = AssessmentScore::withoutGlobalScopes()
                ->where('assessment_id', $assessment->id)
                ->where('student_enrollment_id', $enrollmentId)
                ->first();

            if (! $score || ! $assessment->max_score || (float) $assessment->max_score <= 0) {
                continue;
            }

            $pct = ((float) $score->raw_score / (float) $assessment->max_score) * 100;
            $byType[$assessment->assessment_type_id][] = $pct;
        }

        $total = 0.0;
        $weightSum = 0.0;

        if ($weights->isEmpty()) {
            $all = collect($byType)->flatten();
            $total = $all->avg() ?? 0;
            $weightSum = 100;
        } else {
            foreach ($weights as $typeId => $weight) {
                $scores = $byType[$typeId] ?? [];
                if ($scores === []) {
                    continue;
                }
                $avg = array_sum($scores) / count($scores);
                $w = (float) $weight->weight_percent;
                $total += $avg * ($w / 100);
                $weightSum += $w;
            }
        }

        if ($weightSum > 0 && $weightSum < 99.5) {
            // normalize if partial weights applied
            $total = $total * (100 / $weightSum);
        }

        $scale = $this->schemes->ensureDefaultScale($schoolId, $scheme);
        $band = $this->bandForPercent($scale, $total);

        return SubjectTermResult::withoutGlobalScopes()->updateOrCreate(
            [
                'student_enrollment_id' => $enrollmentId,
                'subject_id' => $subjectId,
                'term_id' => $termId,
            ],
            [
                'school_id' => $schoolId,
                'total_percent' => round($total, 2),
                'letter_grade' => $band?->label,
                'band_code' => $band?->code,
                'gpa_points' => $band?->gpa_points,
                'teacher_comment' => $teacherComment,
            ]
        );
    }

    private function majorityBand(Collection $ratings): ?string
    {
        if ($ratings->isEmpty()) {
            return null;
        }

        $counts = $ratings->countBy()->all();
        arsort($counts);
        $top = array_key_first($counts);

        return $top !== null ? (string) $top : null;
    }

    private function bandForPercent(GradeScale $scale, float $percent): ?\App\Models\GradeScaleBand
    {
        foreach ($scale->bands as $band) {
            if ($band->min_score === null || $band->max_score === null) {
                continue;
            }
            if ($percent >= (float) $band->min_score && $percent <= (float) $band->max_score) {
                return $band;
            }
        }

        return null;
    }
}
