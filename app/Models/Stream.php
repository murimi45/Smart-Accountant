<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ScopedViaClass;

class Stream extends Model
{
    use HasFactory, ScopedViaClass;

    protected $fillable = ['class_id', 'name'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentEnrollment::class,
            'stream_id',
            'id',
            'id',
            'student_id'
        );
    }
}
