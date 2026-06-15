<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;

class Transaction extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'invoice_id',
        'reference',
        'phone',
        'amount',
        'status',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}


