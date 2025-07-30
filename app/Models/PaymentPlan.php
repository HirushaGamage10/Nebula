<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'course_id',
        'intake_id',
        'registration_fee',
        'local_fee',
        'international_fee',
        'international_currency',
        'sscl_tax',
        'bank_charges',
        'apply_discount',
        'discount',
        'installment_plan',
        'installments',
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'local_fee' => 'decimal:2',
        'international_fee' => 'decimal:2',
        'sscl_tax' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'apply_discount' => 'boolean',
        'installment_plan' => 'boolean',
        'installments' => 'array',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }
}
