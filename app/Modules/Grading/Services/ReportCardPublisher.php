<?php

namespace App\Modules\Grading\Services;

use App\Models\ClassSubject;
use App\Models\ReportCardItem;
use App\Models\StudentEnrollment;
use App\Models\SubjectTermResult;
use App\Models\TermReportCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReportCardPublisher
{
    public function __construct(
        private ResultsEngine $engine,
        private SchemeResolver $schemes,
        private ReportCardPdf $pdf,
    ) {}

    public function publish(TermReportCard $card, User $publisher): TermReportCard
    {
        if ($card->isPublished()) {
            throw new RuntimeException('Report card is already published. Unpublish first to regenerate.');
        }

        $enrollment = StudentEnrollment::withoutGlobalScopes()->findOrFail($card->student_enrollment_id);
        $scheme = $this->schemes->schemeForTerm((int) $card->school_id, (int) $card->term_id);
        if (! $scheme) {
            throw new RuntimeException('No grading scheme configured for this academic year.');
        }

        return DB::transaction(function () use ($card, $publisher, $enrollment, $scheme) {
            $subjectIds = ClassSubject::withoutGlobalScopes()
                ->where('school_id', $card->school_id)
                ->where('class_id', $enrollment->class_id)
                ->where(function ($q) use ($enrollment, $card) {
                    $yearId = DB::table('terms')->where('id', $card->term_id)->value('academic_year_id');
                    $q->where('academic_year_id', $yearId)
                        ->where(function ($q2) use ($card) {
                            $q2->whereNull('term_id')->orWhere('term_id', $card->term_id);
                        });
                })
                ->pluck('subject_id')
                ->unique();

            $results = collect();
            foreach ($subjectIds as $subjectId) {
                $results->push($this->engine->computeForEnrollment(
                    (int) $card->school_id,
                    (int) $enrollment->id,
                    (int) $card->term_id,
                    (int) $subjectId
                ));
            }

            if ($scheme->isInternational()) {
                $mean = $results->avg(fn ($r) => (float) ($r->total_percent ?? 0));
                $card->term_mean = $mean !== null ? round($mean, 2) : null;
            }

            $card->items()->delete();
            $sort = 0;
            foreach ($results as $result) {
                $subject = $result->subject()->first() ?? \App\Models\Subject::withoutGlobalScopes()->find($result->subject_id);
                ReportCardItem::query()->create([
                    'term_report_card_id' => $card->id,
                    'subject_id' => $result->subject_id,
                    'subject_name' => $subject?->name ?? 'Subject',
                    'total_percent' => $result->total_percent,
                    'letter_grade' => $result->letter_grade,
                    'band_code' => $result->band_code,
                    'gpa_points' => $result->gpa_points,
                    'teacher_comment' => $result->teacher_comment,
                    'sort_order' => $sort++,
                ]);

                $result->locked_at = now();
                $result->save();
            }

            $card->scheme_snapshot = [
                'slug' => $scheme->slug,
                'name' => $scheme->name,
            ];
            $card->status = TermReportCard::STATUS_PUBLISHED;
            $card->published_at = now();
            $card->published_by = $publisher->id;
            $card->save();

            $path = $this->pdf->store($card->fresh(['items', 'enrollment.student', 'term']));
            $card->pdf_path = $path;
            $card->save();

            return $card->fresh(['items']);
        });
    }

    public function unpublish(TermReportCard $card): TermReportCard
    {
        if (! $card->isPublished()) {
            return $card;
        }

        return DB::transaction(function () use ($card) {
            SubjectTermResult::withoutGlobalScopes()
                ->where('school_id', $card->school_id)
                ->where('student_enrollment_id', $card->student_enrollment_id)
                ->where('term_id', $card->term_id)
                ->update(['locked_at' => null]);

            if ($card->pdf_path) {
                Storage::disk('local')->delete($card->pdf_path);
            }

            $card->status = TermReportCard::STATUS_DRAFT;
            $card->published_at = null;
            $card->published_by = null;
            $card->pdf_path = null;
            $card->save();

            return $card;
        });
    }
}
