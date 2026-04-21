<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'name'];

    // Stream belongs to a grade
  public function class(){

        return $this->belongsTo(Classes::class,'class_id');
    }

    // Stream has many students
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
