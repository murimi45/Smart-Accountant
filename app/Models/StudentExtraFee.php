<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToSchool;

class StudentExtraFee extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;
    protected $table = 'extra_fee_assignments';

    protected $fillable=['student_id',
        'extra_fee_id',
        'amount',
        'quantity',
        'school_id',
        'created_by',];


    protected static function booted()
    {
        static::creating(function (self $assignment) {
            if ($assignment->school_id || ! $assignment->student_id) {
                return;
            }

            $assignment->school_id = Student::withoutGlobalScopes()
                ->whereKey($assignment->student_id)
                ->value('school_id');
        });
    }

          public function student()
    {
        return $this->belongsTo(Student::class,'student_id');
    }

    public function extraFee()
    {
        return $this->belongsTo(ExtraFee::class,'extra_fee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
    
}
