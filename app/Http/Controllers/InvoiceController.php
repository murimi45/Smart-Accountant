<?php

namespace App\Http\Controllers;
use App\Models\Invoice;
use App\Models\Classes;
use App\Models\Term;
use App\Models\InvoicePayment;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
{
    $query = Invoice::with(['student.class', 'items', 'payments']);

    // Filter by class
    if ($request->filled('class_id')) {
        $query->whereHas('student', function ($q) use ($request) {
            $q->where('class_id', $request->class_id);
        });
    }

    // Filter by term
    if ($request->filled('term_id')) {
        $query->where('term_id', $request->term_id);
    }

    // Filter by student name (search)
    if ($request->filled('search')) {
        $query->whereHas('student', function ($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->search . '%');
        });
    }

    $invoices = $query->latest()->get();
    $classes  = Classes::all();
    $terms    = Term::all();

    return view('invoices.showinvoice', compact('invoices', 'classes', 'terms'));
}


    public function storePayment(Request $request, Invoice $invoice)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'method' => 'required|string|max:50',
    ]);

    app(\App\Services\InvoiceService::class)
        ->paymentMade($invoice, $request->amount, $request->method);

    return redirect()->route('invoices.index')->with('success', 'Payment recorded successfully.');
}





    //     public function createPayment(Invoice $invoice)
    // {
    //     return view('invoices.createPayment', compact('invoice'));
    // }

    // /**
    //  * Store the payment (simplified).
    //  */
    // public function storePayment(Request $request, Invoice $invoice)
    // {
    //     $request->validate([
    //         'amount' => 'required|numeric|min:1',
    //         'method' => 'required|string|max:50',
    //         'payment_date' => 'required|date',
    //     ]);

    //     // Assuming you have InvoicePayment model
    //     $invoice->payments()->create([
    //         'amount'       => $request->amount,
    //         'method'       => $request->method,
    //         'payment_date' => $request->payment_date,
    //     ]);

    //     // Update invoice balance & status using your InvoiceService
    //     app(\App\Services\InvoiceService::class)->paymentMade($invoice, $request->amount);

    //     return redirect()
    //         ->route('invoices.index')
    //         ->with('success', 'Payment recorded successfully.');
    // }
}



    
    


