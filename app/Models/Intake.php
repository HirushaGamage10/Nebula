<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intake extends Model
{
    use HasFactory;

    protected $table = 'intakes';
    protected $primaryKey = 'intake_id';

    protected $fillable = [
        'location',
        'course_name',
        'batch',
        'batch_size',
        'intake_mode',
        'intake_type',
        'registration_fee',
        'franchise_payment',
        'franchise_payment_currency',
        'course_fee',
        'sscl_tax',
        'bank_charges',
        'start_date',
        'end_date',
        'course_registration_id_pattern',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'intake_id' => 'int',
        'batch_size' => 'int',
        'sscl_tax' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_name', 'course_name');
    }

    // Relationships
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class, 'intake_id', 'intake_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'intake_id', 'intake_id');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'intake_id', 'intake_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, CourseRegistration::class, 'intake_id', 'student_id', 'intake_id', 'student_id');
    }

    // Scopes
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByCourseName($query, $courseName)
    {
        return $query->where('course_name', $courseName);
    }

    public function scopeByIntakeMode($query, $mode)
    {
        return $query->where('intake_mode', $mode);
    }

    public function scopeByIntakeType($query, $type)
    {
        return $query->where('intake_type', $type);
    }

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('batch_size', '>', 0);
    }

    // Accessors
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : 'N/A';
    }

    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return 0;
    }

    public function getFormattedRegistrationFeeAttribute()
    {
        return 'Rs. ' . number_format($this->registration_fee, 2);
    }

    public function getFormattedFranchisePaymentAttribute()
    {
        return 'Rs. ' . number_format($this->franchise_payment, 2);
    }

    public function getFormattedCourseFeeAttribute()
    {
        return 'Rs. ' . number_format($this->course_fee, 2);
    }

    public function getTotalFeeAttribute()
    {
        return $this->registration_fee + $this->franchise_payment + $this->course_fee;
    }

    public function getFormattedTotalFeeAttribute()
    {
        return 'Rs. ' . number_format($this->total_fee, 2);
    }

    public function getLocationTextAttribute()
    {
        $locations = [
            'Welisara' => 'Welisara',
            'Moratuwa' => 'Moratuwa',
            'Peradeniya' => 'Peradeniya'
        ];
        
        return $locations[$this->location] ?? $this->location;
    }

    public function getIntakeModeTextAttribute()
    {
        $modes = [
            'Physical' => 'Physical',
            'Online' => 'Online',
            'Hybrid' => 'Hybrid'
        ];
        
        return $modes[$this->intake_mode] ?? $this->intake_mode;
    }

    public function getIntakeTypeTextAttribute()
    {
        $types = [
            'Fulltime' => 'Full Time',
            'Parttime' => 'Part Time'
        ];
        
        return $types[$this->intake_type] ?? $this->intake_type;
    }

    public function getFullIntakeNameAttribute()
    {
        return "{$this->course_name} - {$this->batch} ({$this->intake_mode} {$this->intake_type})";
    }

    // Methods
    public function isCurrent()
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function isUpcoming()
    {
        return $this->start_date > now();
    }

    public function isPast()
    {
        return $this->end_date < now();
    }

    public function getCurrentStudentCount()
    {
        return $this->registrations()->count();
    }

    public function getAvailableSeats()
    {
        return max(0, $this->batch_size - $this->getCurrentStudentCount());
    }

    public function isFull()
    {
        return $this->getCurrentStudentCount() >= $this->batch_size;
    }

    public function isAvailable()
    {
        return $this->getCurrentStudentCount() < $this->batch_size;
    }
} 