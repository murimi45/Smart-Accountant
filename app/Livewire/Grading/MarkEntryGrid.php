<?php

namespace App\Livewire\Grading;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\CompetencyRating;
use App\Modules\Grading\Services\MarkEntryService;
use App\Modules\Grading\Services\RosterService;
use App\Modules\Grading\Services\SchemeResolver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class MarkEntryGrid extends Component
{
    use AuthorizesRequests;

    public Assessment $assessment;

    /** @var array<int, array{student_enrollment_id:int, student_name:string, raw_score:?string, band_code:?string, comment:?string}> */
    public array $rows = [];

    public bool $competency = false;

    public string $message = '';

    public string $error = '';

    public function mount(Assessment $assessment, SchemeResolver $schemes, RosterService $roster): void
    {
        $this->authorize('view', $assessment);
        $this->assessment = $assessment->load(['assessmentType', 'subject', 'schoolClass', 'term']);

        $scheme = $schemes->schemeForTerm((int) $assessment->school_id, (int) $assessment->term_id);
        $typeIsCompetency = (bool) ($assessment->assessmentType?->is_competency);
        $this->competency = $schemes->usesCompetency($scheme) || $typeIsCompetency;

        $enrollments = $roster->enrollmentsFor(
            (int) $assessment->school_id,
            (int) $assessment->term_id,
            (int) $assessment->class_id,
            $assessment->stream_id ? (int) $assessment->stream_id : null
        );

        $scores = AssessmentScore::query()
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('student_enrollment_id');

        $ratings = CompetencyRating::query()
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('student_enrollment_id');

        $this->rows = $enrollments->map(function ($enrollment) use ($scores, $ratings) {
            $score = $scores->get($enrollment->id);
            $rating = $ratings->get($enrollment->id);

            return [
                'student_enrollment_id' => $enrollment->id,
                'student_name' => $enrollment->student?->full_name ?? 'Student #'.$enrollment->student_id,
                'raw_score' => $score?->raw_score !== null ? (string) $score->raw_score : '',
                'band_code' => $rating?->band_code ?? '',
                'comment' => $rating?->comment ?? '',
            ];
        })->values()->all();
    }

    public function save(MarkEntryService $marks): void
    {
        $this->authorize('enterMarks', $this->assessment);
        $this->message = '';
        $this->error = '';

        try {
            $marks->syncBatch($this->assessment, $this->rows, $this->competency);
            $this->message = 'Marks saved.';
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        return view('grading::livewire.mark-entry-grid');
    }
}
