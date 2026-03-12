<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;


class Invoice extends Model
{
      use HasFactory;

    protected $fillable = [
        'student_id',
        'school_id',
        'term_id',
        'total_amount',
        'amount_paid',
        'base_fee',
        'balance_forward',
        'credit_forward',
        'balance',
        'invoice_date',
        'status',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
 

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
     
    protected static function booted()
    {
        static::creating(function ($invoice) {
            // 1. Prefer logged-in user's school
            if (auth()->check() && auth()->user()->school_id) {
                $invoice->school_id = auth()->user()->school_id;
            } 
            // 2. Fallback: assign from student
            elseif ($invoice->student) {
                $invoice->school_id = $invoice->student->school_id;
            }
        });

        
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id) {
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
    }
    


}
