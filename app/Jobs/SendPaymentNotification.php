<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\IncomeRecordedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use RuntimeException;

class SendPaymentNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $invoiceId,
        public int $schoolId,
        public float $amount
    ) {}

    public function handle(): void
    {
        $invoice = Invoice::withoutGlobalScopes()
            ->with('student')
            ->where('school_id', $this->schoolId)
            ->find($this->invoiceId);

        if (! $invoice) {
            throw new RuntimeException('Invoice not found for payment notification.');
        }

        if ((int) $invoice->student?->school_id !== $this->schoolId) {
            throw new RuntimeException('Invoice student does not belong to the expected school.');
        }

        $users = User::where('school_id', $this->schoolId)
            ->whereIn('role', ['admin', 'accountant', 'Admin', 'Accountant'])
            ->get();

        $studentName = $invoice->student?->full_name ?? 'a student';

        foreach ($users as $user) {
            $user->notify(new IncomeRecordedNotification([
                'title' => 'Payment Received',
                'message' => 'You have received KES ' . number_format($this->amount) .
                    ' from ' . $studentName .
                    ' for invoice #' . $invoice->id . '.',
            ]));
        }
    }
}
