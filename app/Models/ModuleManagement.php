<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleManagement extends Model
{
    use HasFactory;

    protected $table = 'module_management';
    protected $primaryKey = 'id';

    protected $fillable = [
        'location',
        'course_id',
        'intake_id',
        'semester',
        'module_id',
        'student_id',
        'specialization',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'course_id' => 'int',
        'intake_id' => 'int',
        'module_id' => 'int',
        'student_id' => 'int',
    ];

    // Constants for location
    const LOCATION_WELISARA = 'Welisara';
    const LOCATION_MORATUWA = 'Moratuwa';
    const LOCATION_PERADENIYA = 'Peradeniya';

    // Constants for semester
    const SEMESTER_1 = '1';
    const SEMESTER_2 = '2';
    const SEMESTER_3 = '3';
    const SEMESTER_4 = '4';
    const SEMESTER_5 = '5';
    const SEMESTER_6 = '6';

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

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    // Scopes
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByIntake($query, $intakeId)
    {
        return $query->where('intake_id', $intakeId);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeWelisara($query)
    {
        return $query->where('location', self::LOCATION_WELISARA);
    }

    public function scopeMoratuwa($query)
    {
        return $query->where('location', self::LOCATION_MORATUWA);
    }

    public function scopePeradeniya($query)
    {
        return $query->where('location', self::LOCATION_PERADENIYA);
    }

    public function scopeByStudentAndSemester($query, $studentId, $semester)
    {
        return $query->where('student_id', $studentId)
                    ->where('semester', $semester);
    }

    public function scopeByCourseAndSemester($query, $courseId, $semester)
    {
        return $query->where('course_id', $courseId)
                    ->where('semester', $semester);
    }

    public function scopeByIntakeAndSemester($query, $intakeId, $semester)
    {
        return $query->where('intake_id', $intakeId)
                    ->where('semester', $semester);
    }

    public function scopeByLocationAndSemester($query, $location, $semester)
    {
        return $query->where('location', $location)
                    ->where('semester', $semester);
    }

    // Static methods
    public static function getLocations()
    {
        return [
            self::LOCATION_WELISARA,
            self::LOCATION_MORATUWA,
            self::LOCATION_PERADENIYA
        ];
    }

    public static function getSemesters()
    {
        return [
            self::SEMESTER_1,
            self::SEMESTER_2,
            self::SEMESTER_3,
            self::SEMESTER_4,
            self::SEMESTER_5,
            self::SEMESTER_6
        ];
    }

    // Methods
    public function isWelisara()
    {
        return $this->location === self::LOCATION_WELISARA;
    }

    public function isMoratuwa()
    {
        return $this->location === self::LOCATION_MORATUWA;
    }

    public function isPeradeniya()
    {
        return $this->location === self::LOCATION_PERADENIYA;
    }

    public function getLocationDisplayName()
    {
        return ucfirst($this->location);
    }

    public function getSemesterDisplayName()
    {
        return "Semester {$this->semester}";
    }

    public function getSemesterNumber()
    {
        return (int) $this->semester;
    }

    // Helper methods for statistics
    public static function getStudentCountByLocation()
    {
        return self::selectRaw('location, COUNT(DISTINCT student_id) as count')
                   ->groupBy('location')
                   ->pluck('count', 'location');
    }

    public static function getStudentCountBySemester()
    {
        return self::selectRaw('semester, COUNT(DISTINCT student_id) as count')
                   ->groupBy('semester')
                   ->pluck('count', 'semester');
    }

    public static function getStudentCountByCourse($courseId = null)
    {
        $query = self::selectRaw('course_id, COUNT(DISTINCT student_id) as count')
                     ->groupBy('course_id');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->pluck('count', 'course_id');
    }

    public static function getStudentCountByModule($moduleId = null)
    {
        $query = self::selectRaw('module_id, COUNT(DISTINCT student_id) as count')
                     ->groupBy('module_id');

        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }

        return $query->pluck('count', 'module_id');
    }

    public static function getStudentCountByIntake($intakeId = null)
    {
        $query = self::selectRaw('intake_id, COUNT(DISTINCT student_id) as count')
                     ->groupBy('intake_id');

        if ($intakeId) {
            $query->where('intake_id', $intakeId);
        }

        return $query->pluck('count', 'intake_id');
    }

    public static function getModuleCountByStudent($studentId = null)
    {
        $query = self::selectRaw('student_id, COUNT(DISTINCT module_id) as count')
                     ->groupBy('student_id');

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        return $query->pluck('count', 'student_id');
    }

    public static function getStudentModulesBySemester($studentId, $semester = null)
    {
        $query = self::with(['module', 'course', 'intake'])
                     ->where('student_id', $studentId);

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    public static function getStudentsByModuleAndSemester($moduleId, $semester = null)
    {
        $query = self::with(['student', 'course', 'intake'])
                     ->where('module_id', $moduleId);

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    public static function getModuleDistributionByLocation($location = null)
    {
        $query = self::selectRaw('location, module_id, COUNT(DISTINCT student_id) as student_count')
                     ->groupBy('location', 'module_id');

        if ($location) {
            $query->where('location', $location);
        }

        return $query->get();
    }

    public static function getSemesterProgress($studentId)
    {
        return self::selectRaw('semester, COUNT(DISTINCT module_id) as module_count')
                   ->where('student_id', $studentId)
                   ->groupBy('semester')
                   ->orderBy('semester')
                   ->pluck('module_count', 'semester');
    }

    public static function getStudentSemesterModules($studentId, $semester)
    {
        return self::with(['module', 'course'])
                   ->where('student_id', $studentId)
                   ->where('semester', $semester)
                   ->get();
    }

    public static function getCourseSemesterModules($courseId, $semester)
    {
        return self::with(['module', 'intake'])
                   ->where('course_id', $courseId)
                   ->where('semester', $semester)
                   ->get();
    }

    public static function getIntakeSemesterModules($intakeId, $semester)
    {
        return self::with(['module', 'course'])
                   ->where('intake_id', $intakeId)
                   ->where('semester', $semester)
                   ->get();
    }

    public static function getLocationSemesterModules($location, $semester)
    {
        return self::with(['module', 'course', 'intake'])
                   ->where('location', $location)
                   ->where('semester', $semester)
                   ->get();
    }

    // Validation methods
    public static function isStudentEnrolledInModule($studentId, $moduleId, $semester = null)
    {
        $query = self::where('student_id', $studentId)
                    ->where('module_id', $moduleId);

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->exists();
    }

    public static function getStudentEnrolledModules($studentId, $semester = null)
    {
        $query = self::with(['module', 'course', 'intake'])
                     ->where('student_id', $studentId);

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }

    public static function getModuleEnrolledStudents($moduleId, $semester = null)
    {
        $query = self::with(['student', 'course', 'intake'])
                     ->where('module_id', $moduleId);

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->get();
    }
} 