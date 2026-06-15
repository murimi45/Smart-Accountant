<?php

namespace Database\Seeders;

use App\Models\Schools;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsTableSeeder extends Seeder
{
    public function run(?int $schoolId = null): void
    {
        $schoolId ??= Schools::query()->value('id');

        if (! $schoolId) {
            return;
        }

        $defaultAccounts = [
            ['name' => 'Cash at Hand', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Cash at Bank', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Accounts Receivable (Fees)', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Furniture & Equipment', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Buildings', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Land', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['name' => 'Accounts Payable', 'category' => 'liability', 'normal_balance' => 'credit'],
            ['name' => 'Salaries Payable', 'category' => 'liability', 'normal_balance' => 'credit'],
            ['name' => 'Fees Received in Advance', 'category' => 'liability', 'normal_balance' => 'credit'],
            ['name' => 'Bank Loan', 'category' => 'liability', 'normal_balance' => 'credit'],
            ['name' => 'Accumulated Fund', 'category' => 'equity', 'normal_balance' => 'credit'],
            ['name' => 'Opening Balance Equity', 'category' => 'equity', 'normal_balance' => 'credit'],
            ['name' => 'Tuition Fees Income', 'category' => 'income', 'normal_balance' => 'credit'],
            ['name' => 'Boarding Income', 'category' => 'income', 'normal_balance' => 'credit'],
            ['name' => 'Transport Income', 'category' => 'income', 'normal_balance' => 'credit'],
            ['name' => 'Other School Income', 'category' => 'income', 'normal_balance' => 'credit'],
            ['name' => 'Donations & Grants', 'category' => 'income', 'normal_balance' => 'credit'],
            ['name' => 'Salaries & Wages', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Teaching Materials', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Transport Expense', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Boarding & Meals', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Repairs & Maintenance', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Utilities', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Depreciation', 'category' => 'expense', 'normal_balance' => 'debit'],
            ['name' => 'Miscellaneous Expense', 'category' => 'expense', 'normal_balance' => 'debit'],
        ];

        foreach ($defaultAccounts as $account) {
            DB::table('accounts')->insert([
                'school_id'      => $schoolId,
                'name'           => $account['name'],
                'category'       => $account['category'],
                'normal_balance' => $account['normal_balance'],
                'is_default'     => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
