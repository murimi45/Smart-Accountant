<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Classes;
use App\Models\Term;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);

        $query = Invoice::with([
                'student',
                'enrollment.schoolClass',
                'enrollment.stream',
                'term',
                'items',
                'payments',
            ])
            ->excludeVoided();

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
            $isCurrentTerm = $currentTerm
                && (int) $request->term_id === (int) $currentTerm->id;
            if ($isCurrentTerm) {
                $query->collectible();
            }
        } elseif ($currentTerm) {
            $query->where('term_id', $currentTerm->id)->collectible();
        } else {
            $query->collectible();
        }

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Search by student name or admission — student.full_name (not name)
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('full_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('admission', 'LIKE', '%' . $request->search . '%');
            });
        }

        $invoices = $query->latest()->paginate(25)->withQueryString();
        $classes  = Classes::orderBy('order')->get();
        $terms    = Term::with('academicYear')->orderByDesc('start_date')->get();

        return view('invoices.showinvoice', compact('invoices', 'classes', 'terms', 'currentTerm'));
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:50',
        ]);

        try {
            app(InvoiceService::class)->paymentMade($invoice, $request->amount, $request->method);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.index')->with('success', 'Payment recorded successfully.');
    }
}