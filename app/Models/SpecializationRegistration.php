<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;

class SpecializationRegistration extends Model
{
    use UserTracking;

    protected $fillable = [
        'student_id', 'course_id', 'intake_id', 'location', 'specialization', 'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
