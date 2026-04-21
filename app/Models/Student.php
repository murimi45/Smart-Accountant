<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SchoolScope;


class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=['school_id',
        'name',
        'phone',
        'admission',
        'gender',
        'class_id',
        'term_id',];

    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope);
    }

    // public static function getRecord(){
    //     return self::all();
    // }

    public static function getSingleStudent($id){
        return self::find($id);
    }

    public static function getRecord($request){

        return self::when($request->class_id, fn($q)=>
                      $q->where('class_id',$request->class_id))

                      ->when($request->term_id, fn($q)=>
                      $q->where('term_id',$request->class_id))

                      ->when($request->admission, fn($q)=>
                      $q->where('admission','like','%'.$request->admission.'%'))

                      ->when($request->name, fn($q)=>
                      $q->where('name','like','%'.$request->name.'%'))

                      ->get();


    }

    public function class(){

        return $this->belongsTo(Classes::class,'class_id');
    }

     public function term(){

        return $this->belongsTo(Term::class);
    }


      public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

     public function enrollments()
        {
            return $this->hasMany(StudentEnrollment::class);
        }
    

    public function currentEnrollment()
{
    return $this->hasOne(StudentEnrollment::class)->latest();
}


   

}
