<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Classes;
use App\Models\Term;
use App\Services\InvoiceService;
use App\Support\TenantFilters;
use Illuminate\Http\Request;
use InvalidArgumentException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = TenantFilters::validate($request);
        $currentTerm = InvoiceService::currentTermForSchool($schoolId);

        $query = Invoice::with([
                'student',
                'enrollment.schoolClass',
                'enrollment.stream',
                'term',
                'items',
                'waivers.requestedBy',
                'waivers.reviewedBy',
                'payments.reversals.reversedBy',
            ])
            ->where('school_id', $schoolId)
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
            $query->whereHas('enrollment', TenantFilters::enrollmentClassFilter(
                $schoolId,
                (int) $request->class_id
            ));
        }

        // Search by student name or admission — student.full_name (not name)
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('full_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('admission', 'LIKE', '%' . $request->search . '%');
            });
        }

        $invoices = $query->latest()->paginate(25)->withQueryString();
        $classes  = Classes::where('school_id', $schoolId)->orderBy('order')->get();
        $terms    = Term::where('school_id', $schoolId)->with('academicYear')->orderByDesc('start_date')->get();
        $pendingWaiverCount = \App\Models\InvoiceWaiver::where('school_id', $schoolId)->pending()->count();

        return view('invoices.showinvoice', compact('invoices', 'classes', 'terms', 'currentTerm', 'pendingWaiverCount'));
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $this->authorize('recordPayment', $invoice);

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

    public function reversePayment(Request $request, Invoice $invoice, InvoicePayment $payment)
    {
        $this->authorize('reversePayment', [$invoice, $payment]);

        if ((int) $payment->invoice_id !== (int) $invoice->id) {
            abort(404);
        }

        $data = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        $amount = isset($data['amount']) ? (float) $data['amount'] : $payment->reversibleAmount();

        try {
            app(InvoiceService::class)->reversePayment(
                $payment,
                $amount,
                $data['reason'],
                (int) auth()->id()
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.index')->with('success', 'Payment reversal recorded.');
    }
}