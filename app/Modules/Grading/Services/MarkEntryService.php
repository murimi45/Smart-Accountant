<?php

namespace App\Modules\Grading\Services;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\CompetencyRating;
use App\Models\TermReportCard;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MarkEntryService
{
    public function upsertScore(Assessment $assessment, int $enrollmentId, float $rawScore): AssessmentScore
    {
        $this->assertWritable($assessment);

        if ($assessment->max_score !== null && $rawScore > (float) $assessment->max_score) {
            throw new InvalidArgumentException('Score exceeds max score for this assessment.');
        }

        if ($rawScore < 0) {
            throw new InvalidArgumentException('Score cannot be negative.');
        }

        return AssessmentScore::withoutGlobalScopes()->updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_enrollment_id' => $enrollmentId,
            ],
            [
                'school_id' => $assessment->school_id,
                'raw_score' => $rawScore,
            ]
        );
    }

    public function upsertCompetency(Assessment $assessment, int $enrollmentId, string $bandCode, ?string $comment = null): CompetencyRating
    {
        $this->assertWritable($assessment);

        $bandCode = strtoupper($bandCode);
        if (! in_array($bandCode, CompetencyRating::BANDS, true)) {
            throw new InvalidArgumentException('Invalid competency band code.');
        }

        return CompetencyRating::withoutGlobalScopes()->updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_enrollment_id' => $enrollmentId,
            ],
            [
                'school_id' => $assessment->school_id,
                'band_code' => $bandCode,
                'comment' => $comment,
            ]
        );
    }

    public function assertWritable(Assessment $assessment): void
    {
        if (! $assessment->isOpen()) {
            throw new InvalidArgumentException('Assessment is not open for mark entry.');
        }

        $published = TermReportCard::withoutGlobalScopes()
            ->where('school_id', $assessment->school_id)
            ->where('term_id', $assessment->term_id)
            ->where('status', TermReportCard::STATUS_PUBLISHED)
            ->whereHas('enrollment', function ($q) use ($assessment) {
                $q->withoutGlobalScopes()
                    ->where('class_id', $assessment->class_id)
                    ->when($assessment->stream_id, fn ($q2) => $q2->where('stream_id', $assessment->stream_id));
            })
            ->exists();

        if ($published) {
            throw new InvalidArgumentException('Results are locked after report card publish.');
        }
    }

    public function syncBatch(Assessment $assessment, array $rows, bool $competency): void
    {
        DB::transaction(function () use ($assessment, $rows, $competency) {
            foreach ($rows as $row) {
                $enrollmentId = (int) ($row['student_enrollment_id'] ?? 0);
                if ($enrollmentId <= 0) {
                    continue;
                }

                if ($competency) {
                    $band = $row['band_code'] ?? null;
                    if ($band === null || $band === '') {
                        continue;
                    }
                    $this->upsertCompetency($assessment, $enrollmentId, (string) $band, $row['comment'] ?? null);
                } else {
                    if (! array_key_exists('raw_score', $row) || $row['raw_score'] === '' || $row['raw_score'] === null) {
                        continue;
                    }
                    $this->upsertScore($assessment, $enrollmentId, (float) $row['raw_score']);
                }
            }
        });
    }
}
