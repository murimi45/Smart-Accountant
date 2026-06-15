<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use App\Models\Concerns\BelongsToSchool;


class ClassFee extends Model
{
    use softDeletes, BelongsToSchool;

    protected $fillable=['school_id',
        'class_id',
        'term_id',
        'year',
        'amount',
        'description','status'];


        public function class()
        {
            return $this->belongsTo(Classes::class);
        }

        public function term(){
            return $this->belongsTo(Term::class);
        }
}
