<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SchoolScope;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'name', 'is_current', 'start_date', 'end_date'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }
}