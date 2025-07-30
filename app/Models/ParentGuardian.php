<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentGuardian extends Model
{
    use HasFactory;

    protected $table = 'guardian_details';
    protected $primaryKey = 'guardian_id';

    protected $fillable = [
        'student_id',
        'guardian_name',
        'guardian_profession',
        'guardian_contact_number',
        'guardian_email',
        'guardian_address',
        'emergency_contact_number',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'guardian_id' => 'int',
        'student_id' => 'int',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scopes
    public function scopeByProfession($query, $profession)
    {
        return $query->where('guardian_profession', 'like', "%{$profession}%");
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return ucwords(strtolower($this->guardian_name));
    }

    public function getFormattedContactNoAttribute()
    {
        return $this->guardian_contact_number ?: 'N/A';
    }

    public function getFormattedEmergencyContactAttribute()
    {
        return $this->emergency_contact_number ?: 'N/A';
    }

    public function getFormattedEmailAttribute()
    {
        return $this->guardian_email ?: 'N/A';
    }

    // Mutators
    public function setGuardianNameAttribute($value)
    {
        $this->attributes['guardian_name'] = ucwords(strtolower($value));
    }

    public function setGuardianEmailAttribute($value)
    {
        $this->attributes['guardian_email'] = strtolower($value);
    }

    public function setGuardianContactNumberAttribute($value)
    {
        $this->attributes['guardian_contact_number'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setEmergencyContactNumberAttribute($value)
    {
        $this->attributes['emergency_contact_number'] = preg_replace('/[^0-9]/', '', $value);
    }
}
