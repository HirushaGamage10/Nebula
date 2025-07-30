<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;

    protected $table = 'exam_results';
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'course_id',
        'module_id',
        'intake_id',
        'location',
        'semester',
        'marks',
        'grade',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'course_id' => 'int',
        'module_id' => 'int',
        'intake_id' => 'int',
        'marks' => 'int',
    ];

    /**
     * Get the student that owns the exam result
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'intake_id');
    }

    public function registration()
    {
        return $this->belongsTo(CourseRegistration::class, 'registration_id', 'id');
    }

    /**
     * Get the O/L subjects for the exam result
     */
    public function olSubjects(): HasMany
    {
        return $this->hasMany(OLSubject::class, 'exam_result_id');
    }

    /**
     * Get the A/L subjects for the exam result
     */
    public function alSubjects(): HasMany
    {
        return $this->hasMany(ALSubject::class, 'exam_result_id');
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    public function scopeByIntake($query, $intakeId)
    {
        return $query->where('intake_id', $intakeId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopeHighPerformers($query, $minMarks = 80)
    {
        return $query->where('marks', '>=', $minMarks);
    }

    public function scopeLowPerformers($query, $maxMarks = 40)
    {
        return $query->where('marks', '<', $maxMarks);
    }

    // Accessors
    public function getFormattedMarksAttribute()
    {
        return "{$this->marks} marks";
    }

    public function getGradeColorAttribute()
    {
        $colors = [
            'A' => 'success',
            'B' => 'info',
            'C' => 'warning',
            'D' => 'danger',
            'F' => 'dark'
        ];
        
        return $colors[$this->grade] ?? 'secondary';
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

    public function getSemesterTextAttribute()
    {
        return "Semester {$this->semester}";
    }

    // Methods
    public function isPassed()
    {
        return in_array($this->grade, ['A', 'B', 'C', 'D']);
    }

    public function isFailed()
    {
        return $this->grade === 'F';
    }

    public function getPercentage()
    {
        // Assuming marks are out of 100, adjust if different
        return $this->marks;
    }

    // Static methods for statistics
    public static function getAverageMarks($courseId, $moduleId = null, $intakeId = null)
    {
        $query = self::where('course_id', $courseId);
        
        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }
        
        if ($intakeId) {
            $query->where('intake_id', $intakeId);
        }
        
        return $query->avg('marks') ?? 0;
    }

    public static function getPassRate($courseId, $moduleId = null, $intakeId = null)
    {
        $query = self::where('course_id', $courseId);
        
        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }
        
        if ($intakeId) {
            $query->where('intake_id', $intakeId);
        }
        
        $total = $query->count();
        $passed = $query->whereIn('grade', ['A', 'B', 'C', 'D'])->count();
        
        return $total > 0 ? round(($passed / $total) * 100, 2) : 0;
    }

    public static function getGradeDistribution($courseId, $moduleId = null, $intakeId = null)
    {
        $query = self::where('course_id', $courseId);
        
        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }
        
        if ($intakeId) {
            $query->where('intake_id', $intakeId);
        }
        
        return $query->selectRaw('grade, COUNT(*) as count')
                    ->groupBy('grade')
                    ->orderBy('grade')
                    ->get();
    }
}