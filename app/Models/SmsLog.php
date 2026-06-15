<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class SmsLog extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'to', 'message', 'status', 'response', 'student_id', 'provider_message_id'];

    protected static function booted()
    {
        static::creating(function (self $log) {
            if ($log->school_id) {
                return;
            }

            if ($log->student_id) {
                $log->school_id = Student::withoutGlobalScopes()
                    ->whereKey($log->student_id)
                    ->value('school_id');

                if ($log->school_id) {
                    return;
                }
            }

            if (auth()->check() && auth()->user()->school_id) {
                $log->school_id = auth()->user()->school_id;
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
