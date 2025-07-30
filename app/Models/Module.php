<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $primaryKey = 'module_id';

    protected $fillable = [
        'module_code',
        'module_name',
        'module_type',
        'module_cordinator',
        'credits',
    ];

    protected $casts = [
        'credits' => 'int',
    ];

    // Automatically manage timestamps
    public $timestamps = true;

    // Relationships
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_modules', 'module_id', 'course_id')
                   ->withPivot('is_core', 'credits')
                   ->withTimestamps();
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'module_id', 'module_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'module_id', 'module_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeCore($query)
    {
        return $query->whereHas('courses', function($q) {
            $q->where('is_core', true);
        });
    }

    public function scopeElective($query)
    {
        return $query->whereHas('courses', function($q) {
            $q->where('is_core', false);
        });
    }

    // Accessors
    public function getFullModuleNameAttribute()
    {
        return "{$this->module_name} ({$this->module_code})";
    }

    public function getCreditsFormattedAttribute()
    {
        return "{$this->credits} credits";
    }

    // Mutators
    public function setModuleNameAttribute($value)
    {
        $this->attributes['module_name'] = ucwords(strtolower($value));
    }

    public function setModuleCodeAttribute($value)
    {
        $this->attributes['module_code'] = strtoupper($value);
    }
}
