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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'semester_module', 'semester_id', 'module_id');
    }
}
