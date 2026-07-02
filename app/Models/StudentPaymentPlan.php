<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;

class StudentPaymentPlan extends Model
{
    use HasFactory, UserTracking;
    protected $fillable = [
        'student_id',
        'course_id',
        'payment_plan_type',
        'slt_loan_applied',
        'slt_loan_amount',
        'slt_loan_start_installment',
        'slt_loan_years',
        'slt_receivable_effective_date',
        'total_amount',
        'final_amount',
        'remaining_registration_discount',
        'status',
        'discounts', 'registration_fee_discount',
    ];

    protected $casts = [
        'slt_loan_amount' => 'decimal:2',
        'slt_loan_start_installment' => 'integer',
        'slt_loan_years' => 'integer',
        'slt_receivable_effective_date' => 'date',
        'total_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'remaining_registration_discount' => 'decimal:2',

        'discounts' => 'array',
        'registration_fee_discount' => 'array',
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
