<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Term;
use App\Services\AgedDebtorsReportService;
use App\Services\InvoiceService;
use App\Support\TenantFilters;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgedDebtorsController extends Controller
{
    public function index(Request $request, AgedDebtorsReportService $reportService)
    {
        $schoolId = TenantFilters::validate($request);
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);

        $termId = $request->filled('term_id')
            ? (int) $request->term_id
            : ($currentTerm?->id);

        $classId = $request->filled('class_id') ? (int) $request->class_id : null;

        $report = $reportService->build($schoolId, $termId, $classId);

        $classes = Classes::where('school_id', $schoolId)->orderBy('order')->get();
        $terms = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();

        $selectedTerm = $termId ? $terms->firstWhere('id', $termId) : null;
        $selectedClass = $classId ? $classes->firstWhere('id', $classId) : null;

        return view('reports.aged-debtors.index', compact(
            'report',
            'classes',
            'terms',
            'currentTerm',
            'termId',
            'classId',
            'selectedTerm',
            'selectedClass'
        ));
    }

    public function exportPdf(Request $request, AgedDebtorsReportService $reportService)
    {
        $payload = $this->reportPayload($request, $reportService);

        $pdf = Pdf::loadView('reports.aged-debtors.pdf', $payload)
            ->setPaper('a4', 'landscape');

        $filename = 'Aged-Debtors-'.$payload['report']['asOf']->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request, AgedDebtorsReportService $reportService): StreamedResponse
    {
        $payload = $this->reportPayload($request, $reportService);
        $report = $payload['report'];
        $school = auth()->user()->school;

        $filename = 'Aged-Debtors-'.$report['asOf']->format('Y-m-d').'.xls';

        return response()->streamDownload(function () use ($report, $school, $payload) {
            echo view('reports.aged-debtors.excel', array_merge($payload, [
                'schoolName' => $school->school_name ?? 'School',
            ]))->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /** @return array<string, mixed> */
    private function reportPayload(Request $request, AgedDebtorsReportService $reportService): array
    {
        $schoolId = TenantFilters::validate($request);
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);

        $termId = $request->filled('term_id')
            ? (int) $request->term_id
            : ($currentTerm?->id);

        $classId = $request->filled('class_id') ? (int) $request->class_id : null;

        $report = $reportService->build($schoolId, $termId, $classId);

        $terms = Term::where('school_id', $schoolId)->with('academicYear')->get();
        $classes = Classes::where('school_id', $schoolId)->get();

        return [
            'report'        => $report,
            'selectedTerm'  => $termId ? $terms->firstWhere('id', $termId) : null,
            'selectedClass' => $classId ? $classes->firstWhere('id', $classId) : null,
        ];
    }
}
