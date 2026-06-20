<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\InvoicePayment;
use App\Models\OtherIncome;
use App\Models\Schools;
use App\Models\User;
use App\Notifications\DailySummaryRecordedNotification;
use Illuminate\Console\Command;

class SendDailySummary extends Command
{
    protected $signature = 'summary:daily';

    protected $description = 'Send daily income and expense summary to school admins and accountants';

    public function handle()
    {
        $date = today();
        $sent = 0;

        foreach (Schools::query()->pluck('id') as $schoolId) {
            $feeIncome = (float) InvoicePayment::query()
                ->whereHas('invoice', fn ($q) => $q->where('school_id', $schoolId))
                ->whereDate('payment_date', $date)
                ->sum('amount');

            $otherIncome = (float) OtherIncome::query()
                ->where('school_id', $schoolId)
                ->whereDate('income_date', $date)
                ->sum('amount');

            $totalExpense = (float) Expense::query()
                ->where('school_id', $schoolId)
                ->whereDate('expense_date', $date)
                ->sum('amount');

            $totalIncome = $feeIncome + $otherIncome;

            $recipients = User::query()
                ->where('school_id', $schoolId)
                ->whereIn('role', ['admin', 'accountant', 'Admin', 'Accountant'])
                ->get();

            if ($recipients->isEmpty()) {
                continue;
            }

            $message = sprintf(
                'Income: KES %s (fees KES %s, other KES %s) | Expense: KES %s (%s)',
                number_format($totalIncome, 2),
                number_format($feeIncome, 2),
                number_format($otherIncome, 2),
                number_format($totalExpense, 2),
                $date->toFormattedDateString()
            );

            foreach ($recipients as $user) {
                $user->notify(new DailySummaryRecordedNotification([
                    'title' => 'Daily Summary',
                    'message' => $message,
                ]));
                $sent++;
            }
        }

        $this->info("Daily summary notifications sent to {$sent} user(s).");
    }
}
