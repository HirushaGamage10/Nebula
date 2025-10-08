<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'intake_id',
        'badge_title',
        'badge_image_path',
        'verification_code',
        'issued_date',
        'status'
    ];

    public function student() {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function course() {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function intake() {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }
}
