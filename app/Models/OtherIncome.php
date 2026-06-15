<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToSchool;

class OtherIncome extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'income_category_id',
        'description',
        'amount',
        'payment_method',
        'income_date',
        'term_id',
        'year',
        'created_by',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Polymorphic cashbook relationship
    public function cashbookEntries()
    {
        return $this->morphMany(CashbookEntry::class, 'source');
    }
}