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
        'course_registration_id',
        'amount',
        'payment_method',
        'transaction_id',
        'remarks',
        'paid_slip_path',
        'installment_number',
        'due_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'course_registration_id' => 'int',
        'amount' => 'decimal:2',
        'installment_number' => 'int',
        'due_date' => 'date',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function registration()
    {
        return $this->belongsTo(CourseRegistration::class, 'course_registration_id', 'id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseRegistration::class, 'id', 'course_id', 'course_registration_id', 'course_id');
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
        return $query->whereHas('registration', function($q) use ($courseId) {
            $q->where('course_id', $courseId);
        });
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
        return $this->status === 'paid' ? 'Successful' : 'Failed';
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
        return 'Rs. ' . number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : 'N/A';
    }

    // Methods
    public function isSuccessful()
    {
        return $this->status === 'paid';
    }

    public function getPaymentReference()
    {
        return $this->transaction_id;
    }

    public function updatePaymentStatus($status)
    {
        $this->status = $status;
        $this->save();
        
        // Update registration payment status if this is a registration payment
        if ($this->course_registration_id) {
            $this->registration->updatePaymentStatus();
        }
    }
} 