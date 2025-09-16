<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'payment_name',
        'amount',
        'due_date',
        'late_payment_fee',
        'discount_amount',
        'discount_reason',
        'final_amount',
        'status',
        'paid_date',
        'receipt_no',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_payment_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date'
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
}