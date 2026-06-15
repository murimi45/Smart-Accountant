<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class StudentHistory extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'from_class_id',
        'to_class_id',
        'from_term_id',
        'to_term_id',
        'carried_balance',
        'carried_credit',
    ];

    protected static function booted()
    {
        static::creating(function (self $history) {
            if ($history->school_id || ! $history->student_id) {
                return;
            }

            $history->school_id = Student::withoutGlobalScopes()
                ->whereKey($history->student_id)
                ->value('school_id');
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
