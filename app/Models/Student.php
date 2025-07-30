<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    protected $table = 'students';

    protected $fillable = [
        'title',
        'name_with_initials',
        'full_name',
        'gender',
        'id_type',
        'id_value',
        'address',
        'email',
        'mobile_phone',
        'home_phone',
        'whatsapp_phone',
        'birthday',
        'institute_location',
        'foundation_program',
        'special_needs',
        'extracurricular_activities',
        'future_potentials',
        'other_document_upload',
        'remarks',
        'status',
        'btec_completed',
        'marketing_survey',
        'blacklisted',
        'user_photo',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'foundation_program' => 'boolean',
        'btec_completed' => 'boolean',
        'blacklisted' => 'boolean',
        'birthday' => 'date',
    ];

    // Relationships
    public function exams()
    {
        return $this->hasMany(StudentExam::class, 'student_id', 'student_id');
    }

    public function parentGuardian()
    {
        return $this->hasOne(ParentGuardian::class, 'student_id', 'student_id');
    }

    public function courseRegistrations()
    {
        return $this->hasMany(CourseRegistration::class, 'student_id', 'student_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_id', 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentDetail::class, 'student_id', 'student_id');
    }

    public function libraryClearance()
    {
        return $this->hasOne(StudentLibraryClearance::class, 'student_id', 'student_id');
    }

    public function hostelClearance()
    {
        return $this->hasOne(StudentHostelClearance::class, 'student_id', 'student_id');
    }

    public function paymentClearance()
    {
        return $this->hasOne(StudentPaymentClearance::class, 'student_id', 'student_id');
    }

    public function projectClearance()
    {
        return $this->hasOne(StudentProjectClearance::class, 'student_id', 'student_id');
    }

    public function otherInformation()
    {
        return $this->hasOne(StudentOtherInformation::class, 'student_id', 'student_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('blacklisted', false);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('blacklisted', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('institute_location', $location);
    }

    // Accessors
    public function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function getNameWithInitialsAttribute($value)
    {
        return strtoupper($value);
    }

    // Mutators
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function setMobilePhoneAttribute($value)
    {
        $this->attributes['mobile_phone'] = preg_replace('/[^0-9]/', '', $value);
    }
} 