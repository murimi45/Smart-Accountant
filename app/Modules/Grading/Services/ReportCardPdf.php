<?php

namespace App\Modules\Grading\Services;

use App\Models\Schools;
use App\Models\TermReportCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportCardPdf
{
    public function store(TermReportCard $card): string
    {
        $schemeSlug = $card->scheme_snapshot['slug'] ?? 'international';
        $view = $schemeSlug === 'cbc' ? 'grading::pdf.cbc' : 'grading::pdf.international';

        $school = Schools::query()->find($card->school_id);
        $payload = [
            'card' => $card,
            'school' => $school,
            'student' => $card->enrollment?->student,
            'term' => $card->term,
            'items' => $card->items,
        ];

        $pdf = Pdf::loadView($view, $payload)->setPaper('a4');

        $path = sprintf(
            'schools/%d/report-cards/term-%d-enrollment-%d.pdf',
            $card->school_id,
            $card->term_id,
            $card->student_enrollment_id
        );

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}
