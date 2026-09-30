<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToSchool;

class SalaryGrade extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'basic_pay',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
    ];

    public function allowances(): HasMany
    {
        return $this->hasMany(SalaryGradeAllowance::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}