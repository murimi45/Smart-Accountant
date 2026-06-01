<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Model
{
    use HasFactory;

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
        // Auto-assign school_id on create
        static::creating(function ($invoice) {
            if (auth()->check() && auth()->user()->school_id) {
                $invoice->school_id = auth()->user()->school_id;
            } elseif ($invoice->student) {
                $invoice->school_id = $invoice->student->school_id;
            }
        });

        // School isolation scope
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id) {
                $builder->where('invoices.school_id', auth()->user()->school_id);
            }
        });
    }
}