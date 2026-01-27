<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
class CourseChangePayment extends Model
{
    use UserTracking;

    protected $table = 'course_change_payments';
    
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'student_id',
        'old_course_id',
        'old_intake_id',
        'old_payment_plan_id',
        'total_paid_amount',
        'remarks'
    ];
    
    protected $casts = [
        'total_paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
    
    public function oldCourse()
    {
        return $this->belongsTo(Course::class, 'old_course_id', 'course_id');
    }
    
    public function oldIntake()
    {
        return $this->belongsTo(Intake::class, 'old_intake_id', 'intake_id');
    }
    
    public function oldPaymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'old_payment_plan_id', 'id');
    }
}