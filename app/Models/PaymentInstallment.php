<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// app/Models/PaymentInstallment.php
class PaymentInstallment extends Model
{
    use HasFactory;

    protected $table = 'payment_installments';

    protected $fillable = [
        'payment_plan_id',
        'installment_number',
        'due_date',
        'amount',            // legacy base
        'status',
        'base_amount',
        'discount_amount',
        'discount_note',
        'slt_loan_amount',
        'final_amount',
        'paid_date',
    ];

    protected $casts = [
        'due_date'        => 'date',
        'paid_date'       => 'date',
        'amount'          => 'decimal:2',
        'base_amount'     => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'slt_loan_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
    ];

    // Relationships
    public function paymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'payment_plan_id');
    }
    public function plan()
    {
        return $this->belongsTo(\App\Models\StudentPaymentPlan::class, 'payment_plan_id');
    }

    // Accessors / helpers
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedAmountAttribute()
    {
        // show final if available; fallback to base/legacy
        $value = $this->final_amount ?? $this->base_amount ?? $this->amount ?? 0;
        return 'LKR ' . number_format((float)$value, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = ['pending' => 'warning','paid' => 'success','overdue' => 'danger'];
        return $badges[$this->status] ?? 'secondary';
    }
}
