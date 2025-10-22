<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Class;
use App\Models\Term;
use Illuminate\Support\Facades\Storage;
use App\Services\SendSmsService;

class StatementController extends Controller
{


    public function single(Student $student)
    {
        $invoices = $student->invoices()->with(['items', 'payments'])->get();

        $pdf = Pdf::loadView('statements.single', compact('student', 'invoices'));
        return $pdf->download("Statement-{$student->name}.pdf");
    }


    public function bulk(Request $request)
    {
    $query = Student::with(['invoices.items', 'invoices.payments']);

    // Apply filters
    if ($request->has('class_id')) {
        $query->where('class_id', $request->class_id);
    }

    if ($request->has('term_id')) {
        $query->whereHas('invoices', function($q) use ($request) {
            $q->where('term_id', $request->term_id);
        });
    }

    $students = $query->get();

    if ($students->isEmpty()) {
        return back()->with('error', 'No students found for the selected filters.');
    }

    $zipFileName = 'Statements-' . now()->format('Y-m-d') . '.zip';
    $zipPath = storage_path("app/public/{$zipFileName}");

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        foreach ($students as $student) {
            $pdf = Pdf::loadView('statements.single', [
                'student' => $student,
                'invoices' => $student->invoices()->where('term_id', $request->term_id)->get(),
            ]);

            $pdfPath = "Statement-{$student->name}.pdf";
            $zip->addFromString($pdfPath, $pdf->output());
        }
        $zip->close();
    }

    return response()->download($zipPath);
}


public function bulkBalanceStatements(Request $request)
{
    // ✅ Term must be selected
    if (!$request->filled('term_id')) {
        return back()->with('error', 'Please select a term.');
    }

    $query = Student::with(['invoices.items', 'invoices.payments', 'class']);

    // ✅ Filter by class if selected
    if ($request->filled('class_id')) {
        $query->where('class_id', $request->class_id);
    }

    // ✅ Filter by term (required)
    $query->whereHas('invoices', function ($q) use ($request) {
        $q->where('term_id', $request->term_id);
    });

    // ✅ Filter only students with outstanding balances
    $students = $query->get()->filter(function ($student) use ($request) {
        $total = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->items->sum('amount'));
        $paid  = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->payments->sum('amount'));
        return ($total - $paid) > 0;
    });

    if ($students->isEmpty()) {
        return back()->with('info', 'No students with outstanding balances for the selected filters.');
    }

    // ✅ Create ZIP with PDFs
    $zipFileName = 'Balance-Statements-' . now()->format('Y-m-d') . '.zip';
    $zipPath = storage_path("app/public/{$zipFileName}");

    $zip = new \ZipArchive;
    if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {

        foreach ($students as $student) {
            $invoices = $student->invoices->where('term_id', $request->term_id);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('statements.balance', [
                'student' => $student,
                'invoices' => $invoices,
            ]);

            $pdfName = "{$student->adm_no} - {$student->name}.pdf";
            $zip->addFromString($pdfName, $pdf->output());
        }

        $zip->close();
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
}




public function sendBulkBalanceSms(Request $request, SendSmsService $smsService)
{
    
    $query = Student::with(['invoices.items', 'invoices.payments', 'class']);

    if ($request->filled('class_id')) {
        $query->where('class_id', $request->class_id);
    }

    if ($request->filled('term_id')) {
        $query->whereHas('invoices', function ($q) use ($request) {
            $q->where('term_id', $request->term_id);
        });
    } else {
        return back()->with('error', 'Please select a term.');
    }

    $students = $query->get()->filter(function ($student) use ($request) {
        $total = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->items->sum('amount'));
        $paid  = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->payments->sum('amount'));
        return ($total - $paid) > 0;
    });

    foreach ($students as $student) {
        

        $total = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->items->sum('amount'));
        $paid  = $student->invoices->where('term_id', $request->term_id)->sum(fn($i) => $i->payments->sum('amount'));
        $balance = $total - $paid;


        if ($student->phone) {
            $message = "Dear Parent, {$student->name} has an outstanding balance of KES {$balance}. Kindly clear the balance. Thank you.";
            $smsService->send($student->phone, $message);
        }
    }

    return back()->with('success', 'SMS sent to all parents with outstanding balances.');
}



}
