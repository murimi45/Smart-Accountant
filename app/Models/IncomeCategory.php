<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class IncomeCategory extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
         'school_id',
         'name',
         'description',
    ];

    public function incomes()
    {
        return $this->hasMany(OtherIncome::class, 'income_category_id');
    }
}

