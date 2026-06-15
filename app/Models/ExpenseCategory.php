<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;

class ExpenseCategory extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'name', 'description'];
}
