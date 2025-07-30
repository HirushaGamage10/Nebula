<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester_id',
        'course_id',
        'intake_id',
        'location',
        'specialization',
        'status',
        'registration_date',
    ];

    protected $casts = [
        'registration_date' => 'date',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }
}
