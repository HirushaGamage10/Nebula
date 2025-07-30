<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $table = 'payment_details';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'student_id',
        'course_id',
        'registration_id',
        'payment_method',
        'payment_amount',
        'payment_date',
        'payment_reference',
        'payment_status',
        'payment_type',
        'cheque_number',
        'bank_name',
        'account_number',
        'transaction_id',
        'receipt_number',
        'remarks',
        'paid_slip_path',
        'installment_number',
        'due_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'payment_id' => 'int',
        'student_id' => 'int',
        'course_id' => 'int',
        'registration_id' => 'int',
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_status' => 'boolean',
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

    public function registration()
    {
        return $this->belongsTo(CourseRegistration::class, 'registration_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method', 'method_id');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', false);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeCash($query)
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeCheque($query)
    {
        return $query->where('payment_method', 'cheque');
    }

    public function scopeBankTransfer($query)
    {
        return $query->where('payment_method', 'bank_transfer');
    }

    public function scopeOnline($query)
    {
        return $query->where('payment_method', 'online');
    }

    // Accessors
    public function getPaymentStatusTextAttribute()
    {
        return $this->payment_status ? 'Successful' : 'Failed';
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'online' => 'Online Payment',
            'card' => 'Card Payment'
        ];
        
        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rs. ' . number_format($this->payment_amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->payment_date ? $this->payment_date->format('d/m/Y') : 'N/A';
    }

    // Methods
    public function isSuccessful()
    {
        return $this->payment_status;
    }

    public function getPaymentReference()
    {
        return $this->payment_reference ?: $this->receipt_number ?: $this->transaction_id;
    }

    public function updatePaymentStatus($status)
    {
        $this->payment_status = $status;
        $this->save();
        
        // Update registration payment status if this is a registration payment
        if ($this->registration_id) {
            $this->registration->updatePaymentStatus();
        }
    }
} 