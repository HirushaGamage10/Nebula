<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentList extends Model
{
    use HasFactory;

    protected $table = 'student_lists';
    protected $primaryKey = 'id';

    protected $fillable = [
        'location',
        'course_id',
        'intake_id',
        'student_id',
        'type',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'course_id' => 'int',
        'intake_id' => 'int',
        'student_id' => 'int',
    ];

    // Constants for location
    const LOCATION_WELISARA = 'Welisara';
    const LOCATION_PERADENIYA = 'Peradeniya';
    const LOCATION_MORATUWA = 'Moratuwa';

    // Constants for type
    const TYPE_PERMANENT = 'Permanent';
    const TYPE_TEMPORARY = 'Temporary';

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }

    // Scopes
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePermanent($query)
    {
        return $query->where('type', self::TYPE_PERMANENT);
    }

    public function scopeTemporary($query)
    {
        return $query->where('type', self::TYPE_TEMPORARY);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByIntake($query, $intakeId)
    {
        return $query->where('intake_id', $intakeId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeWelisara($query)
    {
        return $query->where('location', self::LOCATION_WELISARA);
    }

    public function scopePeradeniya($query)
    {
        return $query->where('location', self::LOCATION_PERADENIYA);
    }

    public function scopeMoratuwa($query)
    {
        return $query->where('location', self::LOCATION_MORATUWA);
    }

    // Static methods
    public static function getLocations()
    {
        return [
            self::LOCATION_WELISARA,
            self::LOCATION_PERADENIYA,
            self::LOCATION_MORATUWA
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_PERMANENT,
            self::TYPE_TEMPORARY
        ];
    }

    // Methods
    public function isPermanent()
    {
        return $this->type === self::TYPE_PERMANENT;
    }

    public function isTemporary()
    {
        return $this->type === self::TYPE_TEMPORARY;
    }

    public function isWelisara()
    {
        return $this->location === self::LOCATION_WELISARA;
    }

    public function isPeradeniya()
    {
        return $this->location === self::LOCATION_PERADENIYA;
    }

    public function isMoratuwa()
    {
        return $this->location === self::LOCATION_MORATUWA;
    }

    public function getLocationDisplayName()
    {
        return ucfirst($this->location);
    }

    public function getTypeDisplayName()
    {
        return ucfirst($this->type);
    }

    // Helper methods for statistics
    public static function getStudentCountByLocation()
    {
        return self::selectRaw('location, COUNT(*) as count')
                   ->groupBy('location')
                   ->pluck('count', 'location');
    }

    public static function getStudentCountByType()
    {
        return self::selectRaw('type, COUNT(*) as count')
                   ->groupBy('type')
                   ->pluck('count', 'type');
    }

    public static function getStudentCountByCourseAndLocation($courseId = null)
    {
        $query = self::selectRaw('course_id, location, COUNT(*) as count')
                     ->groupBy('course_id', 'location');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->get();
    }

    public static function getStudentCountByIntakeAndLocation($intakeId = null)
    {
        $query = self::selectRaw('intake_id, location, COUNT(*) as count')
                     ->groupBy('intake_id', 'location');

        if ($intakeId) {
            $query->where('intake_id', $intakeId);
        }

        return $query->get();
    }
} 