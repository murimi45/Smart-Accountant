<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\OverdueSmsReminderService;
use App\Support\TenantFilters;
use App\Support\TenantStorage;

class StatementController extends Controller
{
    public function single(Request $request, Student $student)
    {
        $this->authorize('view', $student);

        $schoolId = TenantFilters::schoolId();
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);

        if ($request->filled('term_id')) {
            TenantFilters::validate($request, ['term_id']);
            $termId = (int) $request->term_id;
        } else {
            $termId = $currentTerm?->id;
        }

        $query = Invoice::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->excludeVoided()
            ->with(['items', 'payments', 'enrollment.schoolClass', 'enrollment.stream', 'term'])
            ->orderBy('created_at');

        if ($termId) {
            $query->where('term_id', $termId);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No invoice found for this student in the selected term.');
        }

        $pdf = Pdf::loadView('statements.single', compact('student', 'invoices'));

        return $pdf->download("Statement-{$student->full_name}.pdf");
    }

    public function bulk(Request $request)
    {
        $schoolId = TenantFilters::validate($request);

        $query = Invoice::where('school_id', $schoolId)
            ->with(['student', 'items', 'payments', 'enrollment.schoolClass'])
            ->excludeVoided();

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', TenantFilters::enrollmentClassFilter(
                $schoolId,
                (int) $request->class_id
            ));
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No invoices found for the selected filters.');
        }

        $grouped = $invoices->groupBy('student_id');

        $zipFileName = 'Statements-' . now()->format('Y-m-d') . '.zip';
        $zipRelative = 'exports/statements/'.$zipFileName;
        TenantStorage::ensureDirectory($zipRelative, $schoolId);
        $zipPath     = TenantStorage::absolutePath($zipRelative, $schoolId);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($grouped as $studentInvoices) {
                $student = $studentInvoices->first()->student;

                $pdf = Pdf::loadView('statements.single', [
                    'student'  => $student,
                    'invoices' => $studentInvoices,
                ]);

                $zip->addFromString("Statement-{$student->full_name}.pdf", $pdf->output());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function bulkBalanceStatements(Request $request)
    {
        if (! $request->filled('term_id')) {
            return back()->with('error', 'Please select a term.');
        }

        $schoolId = TenantFilters::validate($request);

        $query = Invoice::where('school_id', $schoolId)
            ->with(['student', 'items', 'payments', 'enrollment.schoolClass', 'term'])
            ->excludeVoided()
            ->where('term_id', $request->term_id)
            ->where('balance', '>', 0);

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', TenantFilters::enrollmentClassFilter(
                $schoolId,
                (int) $request->class_id
            ));
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('info', 'No students with outstanding balances for the selected filters.');
        }

        $grouped = $invoices->groupBy('student_id');

        $zipFileName = 'Balance-Statements-' . now()->format('Y-m-d') . '.zip';
        $zipRelative = 'exports/balance-statements/'.$zipFileName;
        TenantStorage::ensureDirectory($zipRelative, $schoolId);
        $zipPath     = TenantStorage::absolutePath($zipRelative, $schoolId);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($grouped as $studentInvoices) {
                $student = $studentInvoices->first()->student;

                $pdf = Pdf::loadView('statements.balance', [
                    'student'  => $student,
                    'invoices' => $studentInvoices,
                ]);

                $pdfName = "{$student->admission} - {$student->full_name}.pdf";
                $zip->addFromString($pdfName, $pdf->output());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function sendBulkBalanceSms(Request $request, OverdueSmsReminderService $reminderService)
    {
        if (! $request->filled('term_id')) {
            return back()->with('error', 'Please select a term.');
        }

        $schoolId = TenantFilters::validate($request);

        $query = Invoice::where('school_id', $schoolId)
            ->with(['student'])
            ->excludeVoided()
            ->where('term_id', $request->term_id)
            ->where('balance', '>', 0);

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', TenantFilters::enrollmentClassFilter(
                $schoolId,
                (int) $request->class_id
            ));
        }

        $queued = 0;

        foreach ($query->get() as $invoice) {
            if ($reminderService->queueBalanceReminder($invoice)) {
                $queued++;
            }
        }

        return back()->with('success', "SMS sending jobs have been queued for {$queued} student(s) with outstanding balances.");
    }
}
