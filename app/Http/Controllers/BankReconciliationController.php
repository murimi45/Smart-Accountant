<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\BankReconciliationMatch;
use App\Models\CashbookEntry;
use App\Models\InvoicePayment;
use App\Services\BankReconciliationService;
use App\Support\TenantFilters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BankReconciliationController extends Controller
{
    public function index(Request $request, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->subDays(30);
        $to = $request->filled('to') ? Carbon::parse($request->to) : now();
        $method = $request->filled('method') ? $request->method : null;

        $deposits = BankDeposit::forSchool($schoolId)
            ->with(['matches.cashbookEntry.source.invoice.student', 'matches.matchedBy', 'recordedBy'])
            ->when($request->get('deposit_status', 'open') === 'open', fn ($q) =>
                $q->whereIn('status', [BankDeposit::STATUS_UNMATCHED, BankDeposit::STATUS_PARTIAL])
            )
            ->when($request->get('deposit_status') === 'reconciled', fn ($q) =>
                $q->where('status', BankDeposit::STATUS_RECONCILED)
            )
            ->orderByDesc('deposit_date')
            ->paginate(15, ['*'], 'deposit_page')
            ->withQueryString();

        $unmatchedInflows = $service->unmatchedPaymentInflows($schoolId, $method, $from, $to);

        $selectedDeposit = $request->filled('deposit_id')
            ? BankDeposit::forSchool($schoolId)->with('matches.cashbookEntry')->find($request->deposit_id)
            : $service->openDeposits($schoolId)->first();

        $suggestions = $selectedDeposit
            ? collect($service->suggestMatches($selectedDeposit))->keyBy('cashbook_entry_id')
            : collect();

        $summary = [
            'open_deposits'      => BankDeposit::forSchool($schoolId)->whereIn('status', [BankDeposit::STATUS_UNMATCHED, BankDeposit::STATUS_PARTIAL])->count(),
            'unmatched_inflows'  => $unmatchedInflows->count(),
            'unmatched_inflow_total' => $unmatchedInflows->sum('amount'),
            'open_deposit_total' => BankDeposit::forSchool($schoolId)
                ->whereIn('status', [BankDeposit::STATUS_UNMATCHED, BankDeposit::STATUS_PARTIAL])
                ->get()
                ->sum(fn (BankDeposit $d) => $d->remainingAmount()),
        ];

        return view('reconciliation.index', compact(
            'deposits',
            'unmatchedInflows',
            'selectedDeposit',
            'suggestions',
            'summary',
            'from',
            'to',
            'method'
        ));
    }

    public function storeDeposit(Request $request, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        $data = $request->validate([
            'deposit_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'reference'    => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:255',
        ]);

        try {
            $service->recordDeposit(
                $schoolId,
                $request->user(),
                $data['deposit_date'],
                (float) $data['amount'],
                $data['reference'] ?? null,
                $data['description'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bank deposit recorded.');
    }

    public function match(Request $request, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        $data = $request->validate([
            'bank_deposit_id'   => 'required|integer|exists:bank_deposits,id',
            'cashbook_entry_id' => 'required|integer|exists:cashbook_entries,id',
        ]);

        try {
            $service->match($schoolId, $request->user(), (int) $data['bank_deposit_id'], (int) $data['cashbook_entry_id']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()
            ->with('success', 'Payment matched to bank deposit.')
            ->withInput(['deposit_id' => $data['bank_deposit_id']]);
    }

    public function unmatch(BankReconciliationMatch $match, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        try {
            $service->unmatch($schoolId, $match);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Match removed.');
    }

    public function updateDeposit(Request $request, BankDeposit $deposit, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        $data = $request->validate([
            'deposit_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'reference'    => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:255',
        ]);

        try {
            $service->updateDeposit(
                $schoolId,
                $deposit,
                $data['deposit_date'],
                (float) $data['amount'],
                $data['reference'] ?? null,
                $data['description'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()
            ->with('success', 'Bank deposit updated.')
            ->withInput(['deposit_id' => $deposit->id]);
    }

    public function destroyDeposit(BankDeposit $deposit, BankReconciliationService $service)
    {
        $schoolId = TenantFilters::schoolId();

        try {
            $service->deleteDeposit($schoolId, $deposit);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('reconciliation.index')
            ->with('success', 'Bank deposit deleted. Any matched payments are unmatched again.');
    }
}
