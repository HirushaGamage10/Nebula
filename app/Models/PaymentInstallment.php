<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    protected $table = 'payment_installments';

    protected $fillable = [
        'payment_plan_id',
        'installment_number',
        'due_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function paymentPlan()
    {
        return $this->belongsTo(StudentPaymentPlan::class, 'payment_plan_id');
    }

    // Accessors
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedAmountAttribute()
    {
        return 'LKR ' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
}
