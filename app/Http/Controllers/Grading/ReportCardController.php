<?php

namespace App\Http\Controllers\Grading;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Models\TermReportCard;
use App\Modules\Grading\Services\ReportCardPublisher;
use App\Support\TenantRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCardController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TermReportCard::class);

        $cards = TermReportCard::query()
            ->with(['enrollment.student', 'term'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('grading::report-cards.index', [
            'cards' => $cards,
            'terms' => Term::query()->orderBy('term_number')->get(),
            'enrollments' => StudentEnrollment::query()
                ->with('student')
                ->whereIn('status', [StudentEnrollment::STATUS_ACTIVE, StudentEnrollment::STATUS_REPEATING])
                ->orderByDesc('id')
                ->limit(200)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', TermReportCard::class);

        $data = $request->validate([
            'student_enrollment_id' => ['required', TenantRules::enrollments()],
            'term_id' => ['required', TenantRules::terms()],
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        TermReportCard::query()->firstOrCreate(
            [
                'student_enrollment_id' => $data['student_enrollment_id'],
                'term_id' => $data['term_id'],
            ],
            ['status' => TermReportCard::STATUS_DRAFT]
        );

        return back()->with('success', 'Report card draft created.');
    }

    public function publish(TermReportCard $reportCard, ReportCardPublisher $publisher): RedirectResponse
    {
        $this->authorize('publish', $reportCard);

        try {
            $publisher->publish($reportCard, auth()->user());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Report card published.');
    }

    public function unpublish(TermReportCard $reportCard, ReportCardPublisher $publisher): RedirectResponse
    {
        $this->authorize('unpublish', $reportCard);
        $publisher->unpublish($reportCard);

        return back()->with('success', 'Report card unpublished.');
    }

    public function download(TermReportCard $reportCard): StreamedResponse|RedirectResponse
    {
        $this->authorize('view', $reportCard);

        if (! $reportCard->pdf_path || ! Storage::disk('local')->exists($reportCard->pdf_path)) {
            return back()->with('error', 'PDF not available.');
        }

        return Storage::disk('local')->download(
            $reportCard->pdf_path,
            'report-card-'.$reportCard->id.'.pdf'
        );
    }
}
