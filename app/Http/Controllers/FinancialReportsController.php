<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\FinancialReportService;
use App\Services\InvoiceService;
use App\Support\TenantFilters;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use InvalidArgumentException;

class FinancialReportsController extends Controller
{
    public function index(Request $request, FinancialReportService $reportService)
    {
        return $this->renderReport($request, $reportService, false);
    }

    public function exportPdf(Request $request, FinancialReportService $reportService)
    {
        $payload = $this->renderReport($request, $reportService, true);

        $pdf = Pdf::loadView('reports.financial.pdf', $payload)
            ->setPaper('a4', 'portrait');

        $type = $payload['reportType'];
        $filename = str_replace(' ', '-', ucwords(str_replace('-', ' ', $type)))
            .'-'.$payload['period']['end']->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

  /** @return \Illuminate\View\View|array<string, mixed> */
    private function renderReport(Request $request, FinancialReportService $reportService, bool $forExport)
    {
        $schoolId = TenantFilters::validate($request);
        $viewType = $request->get('view', 'term');
        $reportType = $request->get('report', 'trial-balance');

        if (! in_array($reportType, ['trial-balance', 'profit-loss', 'balance-sheet'], true)) {
            $reportType = 'trial-balance';
        }

        $currentTerm = InvoiceService::currentTermForSchool($schoolId);
        $termId = $request->filled('term_id') ? (int) $request->term_id : ($currentTerm?->id);
        $academicYearId = $request->filled('academic_year_id') ? (int) $request->academic_year_id : null;

        try {
            $period = $reportService->resolvePeriod($schoolId, $viewType, $termId, $academicYearId);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('reports.financial')
                ->with('error', $e->getMessage());
        }

        $report = match ($reportType) {
            'profit-loss' => $reportService->profitAndLoss($schoolId, $period['start'], $period['end']),
            'balance-sheet' => $reportService->balanceSheet($schoolId, $period['end']),
            default => $reportService->trialBalance($schoolId, $period['end']),
        };

        $terms = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();
        $academicYears = AcademicYear::whereHas('terms', fn ($q) => $q->where('school_id', $schoolId))
            ->orderByDesc('start_date')
            ->get();

        $payload = [
            'report'         => $report,
            'reportType'     => $reportType,
            'period'         => $period,
            'viewType'       => $viewType,
            'termId'         => $termId,
            'academicYearId' => $academicYearId ?? $period['academicYear']?->id,
            'terms'          => $terms,
            'academicYears'  => $academicYears,
            'currentTerm'    => $currentTerm,
            'selectedTerm'   => $period['term'],
            'selectedYear'   => $period['academicYear'],
        ];

        if ($forExport) {
            return $payload;
        }

        return view('reports.financial.index', $payload);
    }
}
