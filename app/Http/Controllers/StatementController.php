<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Term;
use App\Models\SmsLog;
use App\Jobs\SendSmsJob;

class StatementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SINGLE STUDENT STATEMENT
    | Downloads a PDF statement for one student across all their invoices
    |--------------------------------------------------------------------------
    */
    public function single(Student $student)
    {
        // Load all non-voided invoices for this student through enrollments
        $invoices = Invoice::where('student_id', $student->id)
            ->excludeVoided()
            ->with(['items', 'payments', 'enrollment.schoolClass', 'enrollment.stream', 'term'])
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('statements.single', compact('student', 'invoices'));

        return $pdf->download("Statement-{$student->full_name}.pdf");
    }

    /*
    |--------------------------------------------------------------------------
    | BULK STATEMENTS — ZIP of PDFs per student
    |--------------------------------------------------------------------------
    */
    public function bulk(Request $request)
    {
        $query = Invoice::with(['student', 'items', 'payments', 'enrollment.schoolClass'])
            ->excludeVoided();

        // Filter by term
        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        // Filter by class via enrollment
        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Group invoices by student
        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No invoices found for the selected filters.');
        }

        $grouped = $invoices->groupBy('student_id');

        $zipFileName = 'Statements-' . now()->format('Y-m-d') . '.zip';
        $zipPath     = storage_path("app/public/{$zipFileName}");

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($grouped as $studentId => $studentInvoices) {
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

    /*
    |--------------------------------------------------------------------------
    | BULK BALANCE STATEMENTS — only students with outstanding balances
    |--------------------------------------------------------------------------
    */
    public function bulkBalanceStatements(Request $request)
    {
        if (!$request->filled('term_id')) {
            return back()->with('error', 'Please select a term.');
        }

        $query = Invoice::with(['student', 'items', 'payments', 'enrollment.schoolClass', 'term'])
            ->excludeVoided()
            ->where('term_id', $request->term_id)
            ->where('balance', '>', 0);   // only outstanding balances

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('info', 'No students with outstanding balances for the selected filters.');
        }

        $grouped = $invoices->groupBy('student_id');

        $zipFileName = 'Balance-Statements-' . now()->format('Y-m-d') . '.zip';
        $zipPath     = storage_path("app/public/{$zipFileName}");

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($grouped as $studentId => $studentInvoices) {
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

    /*
    |--------------------------------------------------------------------------
    | BULK BALANCE SMS — queues SMS for students with outstanding balances
    |--------------------------------------------------------------------------
    */
    public function sendBulkBalanceSms(Request $request)
    {
        if (!$request->filled('term_id')) {
            return back()->with('error', 'Please select a term.');
        }

        $query = Invoice::with(['student'])
            ->excludeVoided()
            ->where('term_id', $request->term_id)
            ->where('balance', '>', 0);

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $invoices = $query->get();

        foreach ($invoices as $invoice) {
            $student = $invoice->student;

            if (!$student?->phone) continue;

            // Normalise Kenyan phone number
            $phone = preg_replace('/^0/', '+254', $student->phone);

            $message = "Dear Parent, {$student->full_name} has an outstanding balance of KES " .
                       number_format($invoice->balance, 2) .
                       ". Kindly clear the balance. Thank you.";

            $smsLog = SmsLog::create([
                'to'         => $phone,
                'message'    => $message,
                'status'     => 'pending',
                'student_id' => $student->id,
            ]);

            dispatch(new SendSmsJob($smsLog->id, $phone, $message));
        }

        return back()->with('success', 'SMS sending jobs have been queued for students with outstanding balances.');
    }
}