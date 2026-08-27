<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToSchool;


class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
    'school_id',
    'full_name',
    'guardian_name',
    'phone',
    'admission',
    'gender',
         ];

    // public static function getRecord(){
    //     return self::all();
    // }

    public static function getSingleStudent($id){
        return self::find($id);
    }

    public static function getRecord($request){

        return self::when($request->admission, fn($q)=>
                      $q->where('admission','like','%'.$request->admission.'%'))

                      ->when($request->full_name, fn($q)=>
                      $q->where('full_name','like','%'.$request->full_name.'%'));

                      


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

    /**
     * True if this student has ever had a payment on any invoice (any term).
     * Used to block hard-delete of students with fee history.
     */
    public function hasAnyInvoicePayments(): bool
    {
        return $this->invoices()->whereHas('payments')->exists();
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
