<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlanDiscount extends Model
{
    use HasFactory;

    protected $table = 'payment_plan_discounts';

    protected $fillable = [
        'payment_plan_id',
        'discount_id',
        'discount_type',
        'discount_value',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
    ];

    // Relationships
    public function paymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'payment_plan_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
