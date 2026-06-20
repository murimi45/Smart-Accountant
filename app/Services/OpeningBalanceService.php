<?php

namespace App\Services;

use App\Models\Invoice;
use InvalidArgumentException;

class OpeningBalanceService
{
    public const LINE_PREFIX = 'Opening balance (prior arrears)';

    public function apply(Invoice $invoice, float $amount, ?string $notes = null): Invoice
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Opening balance cannot be negative.');
        }

        if ($invoice->status === Invoice::STATUS_VOIDED) {
            throw new InvalidArgumentException('Cannot apply opening balance to a voided invoice.');
        }

        $invoice->update([
            'imported_opening_balance' => $amount,
            'opening_balance_notes'    => $notes ? trim($notes) : null,
        ]);

        return $this->syncLineItem($invoice->fresh(['items']));
    }

    public function syncLineItem(Invoice $invoice): Invoice
    {
        $invoice->items()->where('is_opening_balance', true)->delete();

        $amount = (float) $invoice->imported_opening_balance;

        if ($amount > 0) {
            $description = self::LINE_PREFIX;
            if ($invoice->opening_balance_notes) {
                $description .= ': '.$invoice->opening_balance_notes;
            }

            $invoice->items()->create([
                'term_id'            => $invoice->term_id,
                'description'        => $description,
                'amount'             => $amount,
                'is_opening_balance' => true,
            ]);
        }

        return $this->recalculateTotals($invoice->fresh(['items']));
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        $total = (float) $invoice->items()->sum('amount');

        $invoice->update([
            'total_amount' => $total,
            'balance'      => $total - (float) $invoice->amount_paid,
            'status'       => app(InvoiceService::class)->calculateStatusPublic($invoice->fresh()),
        ]);

        return $invoice->fresh();
    }
}
