<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\InvoiceWaiver;
use App\Models\User;
use App\Policies\Concerns\ChecksSchoolAccess;

class InvoiceWaiverPolicy
{
    use ChecksSchoolAccess;

    public function viewAny(User $user): bool
    {
        return $this->isFinanceUser($user);
    }

    public function view(User $user, InvoiceWaiver $waiver): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $waiver->school_id);
    }

    public function create(User $user, Invoice $invoice): bool
    {
        return $this->isFinanceUser($user)
            && $this->belongsToSameSchool($user, $invoice->school_id)
            && $invoice->isCollectible();
    }

    public function approve(User $user, InvoiceWaiver $waiver): bool
    {
        return $this->isAdmin($user)
            && $this->belongsToSameSchool($user, $waiver->school_id)
            && $waiver->isPending();
    }

    public function reject(User $user, InvoiceWaiver $waiver): bool
    {
        return $this->approve($user, $waiver);
    }
}
