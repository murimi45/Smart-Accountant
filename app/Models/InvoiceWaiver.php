<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class InvoiceWaiver extends Model
{
    use BelongsToSchool;

    public const SCOPE_LINE = 'line';
    public const SCOPE_INVOICE = 'invoice';

    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'school_id',
        'invoice_id',
        'invoice_item_id',
        'target_description',
        'scope',
        'discount_type',
        'value',
        'computed_amount',
        'reason',
        'status',
        'requested_by',
        'reviewed_by',
        'review_notes',
        'requested_at',
        'reviewed_at',
    ];

    protected $casts = [
        'value'           => 'decimal:2',
        'computed_amount' => 'decimal:2',
        'requested_at'    => 'datetime',
        'reviewed_at'     => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function waiverLineItem()
    {
        return $this->hasOne(InvoiceItem::class, 'invoice_waiver_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
