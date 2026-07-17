<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Support\TenantFilters;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', LedgerEntry::class);

        $schoolId = TenantFilters::validate($request);

        $accounts = Account::where('school_id', $schoolId)->orderBy('name')->get();

        $query = LedgerEntry::where('school_id', $schoolId)
            ->with('account')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($request->filled('account_id')) {
            $accountId = (int) $request->account_id;
            Account::where('school_id', $schoolId)->findOrFail($accountId);
            $query->where('account_id', $accountId);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $entries = $query->paginate(25)->withQueryString();

        $totals = LedgerEntry::where('school_id', $schoolId)
            ->when($request->filled('account_id'), fn ($q) => $q->where('account_id', (int) $request->account_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('transaction_date', '<=', $request->date_to))
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        return view('ledger.index', [
            'entries'      => $entries,
            'accounts'     => $accounts,
            'totalDebit'   => (float) ($totals->total_debit ?? 0),
            'totalCredit'  => (float) ($totals->total_credit ?? 0),
            'accountId'    => $request->get('account_id'),
            'dateFrom'     => $request->get('date_from'),
            'dateTo'       => $request->get('date_to'),
        ]);
    }
}
