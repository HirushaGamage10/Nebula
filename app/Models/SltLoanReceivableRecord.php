<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CastsDateOnly;
use App\Traits\UserTracking;

class SltLoanReceivableRecord extends Model
{
    use HasFactory, CastsDateOnly, UserTracking;

    protected $fillable = [
        'student_payment_plan_id',
        'student_id',
        'course_id',
        'loan_installment_number',
        'total_loan_amount',
        'loan_taken_years',
        'loan_installment_count',
        'apply_from_installment',
        'monthly_receivable_amount',
        'payment_effective_date',
        'payment_method',
        'receipt_no',
        'status',
        'remarks',
    ];

    protected $casts = [
        'total_loan_amount' => 'decimal:2',
        'loan_taken_years' => 'integer',
        'loan_installment_count' => 'integer',
        'apply_from_installment' => 'integer',
        'monthly_receivable_amount' => 'decimal:2',
        'payment_effective_date' => 'date',
    ];

    public function studentPaymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'student_payment_plan_id');
    }
}
