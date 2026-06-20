<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceWaiver;
use App\Services\InvoiceWaiverService;
use App\Support\TenantFilters;
use Illuminate\Http\Request;
use InvalidArgumentException;

class InvoiceWaiverController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', InvoiceWaiver::class);

        $schoolId = TenantFilters::schoolId();

        $query = InvoiceWaiver::with([
                'invoice.student',
                'invoice.enrollment.schoolClass',
                'requestedBy',
                'reviewedBy',
            ])
            ->where('school_id', $schoolId)
            ->latest('requested_at');

        if ($request->get('status', 'pending') !== 'all') {
            $query->where('status', $request->get('status', 'pending'));
        }

        $waivers = $query->paginate(25)->withQueryString();
        $pendingCount = InvoiceWaiver::where('school_id', $schoolId)->pending()->count();

        return view('waivers.index', compact('waivers', 'pendingCount'));
    }

    public function store(Request $request, Invoice $invoice, InvoiceWaiverService $service)
    {
        $this->authorize('create', [InvoiceWaiver::class, $invoice]);

        $data = $request->validate([
            'scope'          => 'required|in:line,invoice',
            'discount_type'  => 'required|in:fixed,percentage',
            'value'          => 'required|numeric|min:0.01',
            'reason'         => 'required|string|max:500',
            'invoice_item_id'=> 'nullable|integer|exists:invoice_items,id',
        ]);

        if ($data['scope'] === 'line' && empty($data['invoice_item_id'])) {
            return back()->with('error', 'Select a fee line for a line-level waiver.');
        }

        if ($data['discount_type'] === 'percentage' && (float) $data['value'] > 100) {
            return back()->with('error', 'Percentage discount cannot exceed 100%.');
        }

        try {
            $service->request(
                $invoice,
                $request->user(),
                $data['scope'],
                $data['discount_type'],
                (float) $data['value'],
                $data['reason'],
                $data['invoice_item_id'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Waiver request submitted for approval.');
    }

    public function approve(Request $request, InvoiceWaiver $waiver, InvoiceWaiverService $service)
    {
        $this->authorize('approve', $waiver);

        $data = $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        try {
            $service->approve($waiver, $request->user(), $data['review_notes'] ?? null);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Waiver approved and applied to the invoice.');
    }

    public function reject(Request $request, InvoiceWaiver $waiver, InvoiceWaiverService $service)
    {
        $this->authorize('reject', $waiver);

        $data = $request->validate([
            'review_notes' => 'required|string|max:500',
        ]);

        try {
            $service->reject($waiver, $request->user(), $data['review_notes']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Waiver request rejected.');
    }
}
