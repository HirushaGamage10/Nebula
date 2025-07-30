<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClearance extends Model
{
    use HasFactory;

    protected $table = 'clearance_forms';
    protected $primaryKey = 'clearance_id';

    protected $fillable = [
        'student_id',
        'clearance_type',
        'status',
        'approved_by',
        'approved_date',
        'remarks',
        'document_path',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'clearance_id' => 'int',
        'student_id' => 'int',
        'approved_by' => 'int',
        'status' => 'boolean',
        'approved_date' => 'datetime',
    ];

    // Clearance types
    const TYPE_LIBRARY = 'library';
    const TYPE_HOSTEL = 'hostel';
    const TYPE_PAYMENT = 'payment';
    const TYPE_PROJECT = 'project';

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('clearance_type', $type);
    }

    public function scopeLibrary($query)
    {
        return $query->where('clearance_type', self::TYPE_LIBRARY);
    }

    public function scopeHostel($query)
    {
        return $query->where('clearance_type', self::TYPE_HOSTEL);
    }

    public function scopePayment($query)
    {
        return $query->where('clearance_type', self::TYPE_PAYMENT);
    }

    public function scopeProject($query)
    {
        return $query->where('clearance_type', self::TYPE_PROJECT);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByApprover($query, $approverId)
    {
        return $query->where('approved_by', $approverId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('approved_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Approved' : 'Pending';
    }

    public function getClearanceTypeTextAttribute()
    {
        $types = [
            self::TYPE_LIBRARY => 'Library Clearance',
            self::TYPE_HOSTEL => 'Hostel Clearance',
            self::TYPE_PAYMENT => 'Payment Clearance',
            self::TYPE_PROJECT => 'Project Clearance'
        ];
        
        return $types[$this->clearance_type] ?? ucfirst($this->clearance_type);
    }

    public function getFormattedApprovedDateAttribute()
    {
        return $this->approved_date ? $this->approved_date->format('d/m/Y H:i') : 'N/A';
    }

    public function getStatusColorAttribute()
    {
        return $this->status ? 'success' : 'warning';
    }

    // Methods
    public function isApproved()
    {
        return $this->status;
    }

    public function isPending()
    {
        return !$this->status;
    }

    public function approve($approverId, $remarks = null)
    {
        $this->status = true;
        $this->approved_by = $approverId;
        $this->approved_date = now();
        $this->remarks = $remarks;
        $this->save();
    }

    public function reject($approverId, $remarks = null)
    {
        $this->status = false;
        $this->approved_by = $approverId;
        $this->approved_date = now();
        $this->remarks = $remarks;
        $this->save();
    }

    // Static methods
    public static function getClearanceTypes()
    {
        return [
            self::TYPE_LIBRARY => 'Library Clearance',
            self::TYPE_HOSTEL => 'Hostel Clearance',
            self::TYPE_PAYMENT => 'Payment Clearance',
            self::TYPE_PROJECT => 'Project Clearance'
        ];
    }

    public static function getStudentClearanceStatus($studentId)
    {
        $clearances = self::where('student_id', $studentId)->get();
        
        $status = [];
        foreach (self::getClearanceTypes() as $type => $label) {
            $clearance = $clearances->where('clearance_type', $type)->first();
            $status[$type] = [
                'label' => $label,
                'status' => $clearance ? $clearance->status : false,
                'approved_date' => $clearance ? $clearance->approved_date : null,
                'remarks' => $clearance ? $clearance->remarks : null
            ];
        }
        
        return $status;
    }

    public static function isStudentFullyCleared($studentId)
    {
        $clearances = self::where('student_id', $studentId)->get();
        $requiredTypes = array_keys(self::getClearanceTypes());
        
        foreach ($requiredTypes as $type) {
            $clearance = $clearances->where('clearance_type', $type)->first();
            if (!$clearance || !$clearance->status) {
                return false;
            }
        }
        
        return true;
    }
} 