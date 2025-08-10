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
        'status',                 // current status: registered | terminated
        'registration_date',

        // --- special approval fields ---
        'desired_status',         // what student should become after approval (e.g., 'registered')
        'approval_status',        // none | pending | approved | rejected
        'approval_reason',        // free-text reason
        'approval_file_path',     // stored file path
        'approval_requested_at',
        'approval_decided_at',
        'approval_decided_by',
        'approval_dgm_comment',
    ];

    protected $casts = [
        'registration_date'     => 'date',
        'approval_requested_at' => 'datetime',
        'approval_decided_at'   => 'datetime',
    ];

    /* ------------ Relationships ------------ */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }

    /* ------------ Scopes (handy for the tab) ------------ */
    public function scopePendingReRegister($q)
    {
        return $q->where('status', 'terminated')
                 ->where('desired_status', 'registered')
                 ->where('approval_status', 'pending');
    }
}