<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Support\TenantFilters;

class AccountController extends Controller
{
    public function index()
    {
        $schoolId = TenantFilters::schoolId();

        $accounts = Account::where('school_id', $schoolId)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $categoryLabels = [
            'asset'     => 'Assets',
            'liability' => 'Liabilities',
            'equity'    => 'Equity',
            'income'    => 'Income',
            'expense'   => 'Expenses',
        ];

        return view('accounts.index', compact('accounts', 'categoryLabels'));
    }
}
