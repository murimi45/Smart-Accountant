<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\SchoolScope;

class Classes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'order',
        'school_id',
        
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order' => 'integer',
    ];

    
    protected static function booted()
    {
        static::addGlobalScope(new SchoolScope());
    }

   
    public static function getRecord()
    {
        return self::orderBy('order')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Get single class by ID (respects SchoolScope)
     */
    public static function getSingle($id)
    {
        return self::find($id);
    }

    // ==============================
    // Relationships
    // ==============================

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classfees()
    {
        return $this->hasMany(ClassFee::class);
    }

    /**
     * Next Class Relationship (Kept for backward compatibility)
     * You can remove this entirely later if not needed anymore.
     */
    public function nextClass()
    {
        return $this->belongsTo(Classes::class, 'next_class_id');
    }

    /**
     * Previous Classes (classes pointing to this as next)
     */
    public function previousClasses()
    {
        return $this->hasMany(Classes::class, 'next_class_id');
    }

    // ==============================
    // Scopes & Helpers
    // ==============================

    /**
     * Scope to get next class by order (useful for promotion logic)
     */
    public function scopeNextByOrder($query)
    {
        return $query->where('order', '>', $this->order)
                     ->orderBy('order')
                     ->first();
    }
}