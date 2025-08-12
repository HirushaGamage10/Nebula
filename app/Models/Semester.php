<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'course_id',
        'intake_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'semester_module', 'semester_id', 'module_id')
            ->withPivot('specialization');
    }
}
