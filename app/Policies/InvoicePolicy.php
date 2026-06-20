<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class InvoicePolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $invoice->school_id);
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }

    public function reversePayment(User $user, Invoice $invoice, InvoicePayment $payment): bool
    {
        return $this->recordPayment($user, $invoice)
            && (int) $payment->invoice_id === (int) $invoice->id;
    }
}
