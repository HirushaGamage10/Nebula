<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'payment_plan_type',
        'slt_loan_applied',
        'slt_loan_amount',
        'total_amount',
        'final_amount',
        'status',
    ];

    protected $casts = [
        'slt_loan_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class, 'payment_plan_id');
    }

    public function discounts()
    {
        return $this->hasMany(PaymentPlanDiscount::class, 'payment_plan_id');
    }
}
