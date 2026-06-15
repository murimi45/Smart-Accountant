<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;

class ExtraFee extends Model
{
     use softDeletes, HasFactory, BelongsToSchool;
     protected $fillable=[
        'name','amount','is_quantity_based', 'description', 'school_id', 'created_by','status','term_id',
        'year',
     ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

      public function class()
        {
            return $this->belongsTo(Classes::class);
        }

        public function term(){
            return $this->belongsTo(Term::class);
        }
}
