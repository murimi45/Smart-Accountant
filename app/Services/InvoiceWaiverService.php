<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceWaiver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceWaiverService
{
    public function request(
        Invoice $invoice,
        User $requester,
        string $scope,
        string $discountType,
        float $value,
        string $reason,
        ?int $invoiceItemId = null
    ): InvoiceWaiver {
        $this->assertWaiverableInvoice($invoice);
        $reason = trim($reason);

        if ($reason === '') {
            throw new InvalidArgumentException('A reason is required for the waiver request.');
        }

        if (! in_array($scope, [InvoiceWaiver::SCOPE_LINE, InvoiceWaiver::SCOPE_INVOICE], true)) {
            throw new InvalidArgumentException('Invalid waiver scope.');
        }

        if (! in_array($discountType, [InvoiceWaiver::TYPE_FIXED, InvoiceWaiver::TYPE_PERCENTAGE], true)) {
            throw new InvalidArgumentException('Invalid discount type.');
        }

        if ($value <= 0) {
            throw new InvalidArgumentException('Waiver value must be greater than zero.');
        }

        if ($discountType === InvoiceWaiver::TYPE_PERCENTAGE && $value > 100) {
            throw new InvalidArgumentException('Percentage discount cannot exceed 100%.');
        }

        $targetDescription = null;

        if ($scope === InvoiceWaiver::SCOPE_LINE) {
            if (! $invoiceItemId) {
                throw new InvalidArgumentException('Select a fee line item for a line-level waiver.');
            }

            $item = $invoice->items()
                ->whereNull('invoice_waiver_id')
                ->find($invoiceItemId);

            if (! $item) {
                throw new InvalidArgumentException('The selected fee line is not available for a waiver.');
            }

            if ((float) $item->amount <= 0) {
                throw new InvalidArgumentException('Waivers can only apply to positive fee lines.');
            }

            $targetDescription = $item->description;
        }

        $preview = $this->computeDiscount($invoice, $scope, $discountType, $value, $targetDescription);

        if ($preview <= 0) {
            throw new InvalidArgumentException('This waiver would not reduce the invoice balance.');
        }

        $this->assertDiscountWithinLimits($invoice, $preview);

        return InvoiceWaiver::createForSchool($invoice->school_id, [
            'invoice_id'          => $invoice->id,
            'invoice_item_id'     => $invoiceItemId,
            'target_description'  => $targetDescription,
            'scope'               => $scope,
            'discount_type'       => $discountType,
            'value'               => $value,
            'reason'              => $reason,
            'status'              => InvoiceWaiver::STATUS_PENDING,
            'requested_by'        => $requester->id,
            'requested_at'        => now(),
        ]);
    }

    public function approve(InvoiceWaiver $waiver, User $reviewer, ?string $reviewNotes = null): InvoiceWaiver
    {
        if (! $waiver->isPending()) {
            throw new InvalidArgumentException('Only pending waivers can be approved.');
        }

        return DB::transaction(function () use ($waiver, $reviewer, $reviewNotes) {
            $waiver = InvoiceWaiver::withoutGlobalScopes()
                ->lockForUpdate()
                ->findOrFail($waiver->id);

            $invoice = Invoice::withoutGlobalScopes()
                ->lockForUpdate()
                ->with(['items'])
                ->findOrFail($waiver->invoice_id);

            $this->assertWaiverableInvoice($invoice);

            $computed = $this->computeDiscount(
                $invoice,
                $waiver->scope,
                $waiver->discount_type,
                (float) $waiver->value,
                $waiver->target_description
            );

            if ($computed <= 0) {
                throw new InvalidArgumentException('The fee line is no longer eligible for this waiver.');
            }

            $this->assertDiscountWithinLimits($invoice, $computed, $waiver->id);

            $waiver->update([
                'status'          => InvoiceWaiver::STATUS_APPROVED,
                'computed_amount' => $computed,
                'reviewed_by'     => $reviewer->id,
                'reviewed_at'     => now(),
                'review_notes'    => $reviewNotes ? trim($reviewNotes) : null,
            ]);

            $this->applyWaiverLineItem($invoice, $waiver->fresh());

            return $waiver->fresh(['requestedBy', 'reviewedBy', 'waiverLineItem']);
        });
    }

    public function reject(InvoiceWaiver $waiver, User $reviewer, string $reviewNotes): InvoiceWaiver
    {
        if (! $waiver->isPending()) {
            throw new InvalidArgumentException('Only pending waivers can be rejected.');
        }

        $reviewNotes = trim($reviewNotes);

        if ($reviewNotes === '') {
            throw new InvalidArgumentException('Review notes are required when rejecting a waiver.');
        }

        $waiver->update([
            'status'       => InvoiceWaiver::STATUS_REJECTED,
            'reviewed_by'  => $reviewer->id,
            'reviewed_at'  => now(),
            'review_notes' => $reviewNotes,
        ]);

        return $waiver->fresh(['requestedBy', 'reviewedBy']);
    }

    public function syncAllApprovedWaivers(Invoice $invoice): void
    {
        $invoice->items()->whereNotNull('invoice_waiver_id')->delete();

        $waivers = InvoiceWaiver::withoutGlobalScopes()
            ->where('invoice_id', $invoice->id)
            ->where('status', InvoiceWaiver::STATUS_APPROVED)
            ->get();

        foreach ($waivers as $waiver) {
            $computed = $this->computeDiscount(
                $invoice->fresh(['items']),
                $waiver->scope,
                $waiver->discount_type,
                (float) $waiver->value,
                $waiver->target_description
            );

            if ($computed <= 0) {
                continue;
            }

            $waiver->update(['computed_amount' => $computed]);
            $this->applyWaiverLineItem($invoice->fresh(['items']), $waiver->fresh());
        }

        $this->recalculateInvoiceTotals($invoice->fresh(['items']));
    }

    private function applyWaiverLineItem(Invoice $invoice, InvoiceWaiver $waiver): void
    {
        $invoice->items()->where('invoice_waiver_id', $waiver->id)->delete();

        $label = $waiver->scope === InvoiceWaiver::SCOPE_LINE
            ? 'Bursary/Waiver ('.$waiver->target_description.')'
            : 'Bursary/Waiver (Invoice)';

        $invoice->items()->create([
            'term_id'           => $invoice->term_id,
            'description'       => $label.': '.$waiver->reason,
            'amount'            => -1 * (float) $waiver->computed_amount,
            'invoice_waiver_id' => $waiver->id,
        ]);

        $this->recalculateInvoiceTotals($invoice->fresh(['items']));
    }

    public function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $total = (float) $invoice->items()->sum('amount');

        $invoice->update([
            'total_amount' => $total,
            'balance'      => $total - (float) $invoice->amount_paid,
            'status'       => app(InvoiceService::class)->calculateStatusPublic($invoice->fresh()),
        ]);
    }

    private function computeDiscount(
        Invoice $invoice,
        string $scope,
        string $discountType,
        float $value,
        ?string $targetDescription
    ): float {
        $base = $scope === InvoiceWaiver::SCOPE_INVOICE
            ? $this->chargeSubtotal($invoice)
            : $this->lineAmount($invoice, $targetDescription);

        if ($base <= 0) {
            return 0.0;
        }

        if ($discountType === InvoiceWaiver::TYPE_PERCENTAGE) {
            return round($base * $value / 100, 2);
        }

        return round(min($value, $base), 2);
    }

    private function chargeSubtotal(Invoice $invoice): float
    {
        return (float) $invoice->items
            ->whereNull('invoice_waiver_id')
            ->sum('amount');
    }

    private function lineAmount(Invoice $invoice, ?string $targetDescription): float
    {
        if (! $targetDescription) {
            return 0.0;
        }

        $item = $invoice->items
            ->whereNull('invoice_waiver_id')
            ->firstWhere('description', $targetDescription);

        return $item ? max(0.0, (float) $item->amount) : 0.0;
    }

    private function assertDiscountWithinLimits(Invoice $invoice, float $newDiscount, ?int $ignoreWaiverId = null): void
    {
        $existingWaivers = (float) InvoiceWaiver::withoutGlobalScopes()
            ->where('invoice_id', $invoice->id)
            ->where('status', InvoiceWaiver::STATUS_APPROVED)
            ->when($ignoreWaiverId, fn ($q) => $q->where('id', '!=', $ignoreWaiverId))
            ->sum('computed_amount');

        $chargeSubtotal = $this->chargeSubtotal($invoice);
        $projectedTotal = $chargeSubtotal - $existingWaivers - $newDiscount;

        if ($projectedTotal < 0) {
            throw new InvalidArgumentException('Total waivers cannot exceed the invoice fee total.');
        }

        if ($projectedTotal < (float) $invoice->amount_paid) {
            throw new InvalidArgumentException(
                'This waiver would reduce the invoice below amounts already paid (KSh '
                .number_format($invoice->amount_paid, 2).').'
            );
        }
    }

    private function assertWaiverableInvoice(Invoice $invoice): void
    {
        if (in_array($invoice->status, [Invoice::STATUS_VOIDED, Invoice::STATUS_TRANSFERRED], true)) {
            throw new InvalidArgumentException('Waivers cannot be applied to voided or transferred invoices.');
        }
    }
}
