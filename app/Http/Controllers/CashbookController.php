<?php
namespace App\Http\Controllers;

use App\Models\CashbookEntry;
use App\Support\TenantFilters;

class CashbookController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CashbookEntry::class);

        $schoolId = TenantFilters::schoolId();

        $entries = CashbookEntry::where('school_id', $schoolId)
            ->latest('transaction_date')
            ->paginate(20);

        // Calculate running balance (optional)
        $balance = CashbookEntry::where('school_id', $schoolId)
            ->selectRaw("
                SUM(CASE WHEN transaction_type = 'inflow' THEN amount ELSE -amount END) as balance
            ")
            ->value('balance');

        return view('cashbook.index', compact('entries', 'balance'));
    }
}
