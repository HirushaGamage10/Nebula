<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseChangeLog extends Model
{
    protected $table = 'course_change_logs';
    
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'student_id',
        'old_intake_id',
        'old_course_id',
        'new_intake_id',
        'new_course_id',
        'old_payment_plan_id',
        'total_paid_amount',
        'changed_by',
        'changed_by_name',
        'changed_at',
        'remarks'
    ];
    
    protected $casts = [
        'total_paid_amount' => 'decimal:2',
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
    
    public function oldIntake()
    {
        return $this->belongsTo(Intake::class, 'old_intake_id', 'intake_id');
    }
    
    public function newIntake()
    {
        return $this->belongsTo(Intake::class, 'new_intake_id', 'intake_id');
    }
    
    public function oldCourse()
    {
        return $this->belongsTo(Course::class, 'old_course_id', 'course_id');
    }
    
    public function newCourse()
    {
        return $this->belongsTo(Course::class, 'new_course_id', 'course_id');
    }
    
    public function oldPaymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'old_payment_plan_id', 'id');
    }
    
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
}