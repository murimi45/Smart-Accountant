<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHistory extends Model
{
    protected $fillable = [
        'student_id',
        'from_class_id',
        'to_class_id',
        'from_term_id',
        'to_term_id',
        'carried_balance',
        'carried_credit',
    ];
}
