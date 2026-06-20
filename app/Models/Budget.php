<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'expense_category_id',
        'term_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
