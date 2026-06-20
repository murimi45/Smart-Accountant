<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\BelongsToSchool;

class Invoice extends Model
{
    use HasFactory, BelongsToSchool;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_VOIDED = 'voided';
    public const STATUS_TRANSFERRED = 'transferred';

    protected $fillable = [
        'student_id',
        'school_id',
        'enrollment_id',        // ✅ new anchor
        'term_id',
        'total_amount',
        'amount_paid',
        'base_fee',
        'balance_forward',
        'imported_opening_balance',
        'opening_balance_notes',
        'credit_forward',
        'balance',
        'invoice_date',
        'status',
        'notes',                // ✅ used when voiding
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'imported_opening_balance' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    // ✅ New — primary relationship now
    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function waivers()
    {
        return $this->hasMany(InvoiceWaiver::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    // Exclude voided invoices from all normal listings
    // Call ->withVoided() to include them when needed (e.g. audit screen)
    public function scopeExcludeVoided(Builder $q): Builder
    {
        return $q->where('status', '!=', self::STATUS_VOIDED);
    }

    public function scopeExcludeTransferred(Builder $q): Builder
    {
        return $q->where('status', '!=', self::STATUS_TRANSFERRED);
    }

    /** Invoices that can still receive payments (current-term listing). */
    public function scopeCollectible(Builder $q): Builder
    {
        return $q->excludeVoided()->excludeTransferred();
    }

    public function isCollectible(): bool
    {
        return ! in_array($this->status, [self::STATUS_VOIDED, self::STATUS_TRANSFERRED], true);
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL SCOPE + AUTO school_id ON CREATE
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function (self $invoice) {
            if ($invoice->school_id || ! $invoice->student_id) {
                return;
            }

            $invoice->school_id = Student::withoutGlobalScopes()
                ->whereKey($invoice->student_id)
                ->value('school_id');
        });
    }
}