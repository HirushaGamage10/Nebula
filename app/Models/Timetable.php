<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $table = 'timetable';
    protected $primaryKey = 'id';

    protected $fillable = [
        'location',
        'course_id',
        'intake_id',
        'semester',
        'date',
        'time',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'course_id' => 'int',
        'intake_id' => 'int',
        'date' => 'date',
        'time' => 'datetime:H:i',
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

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByTime($query, $time)
    {
        return $query->where('time', $time);
    }

    public function scopeByTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('time', [$startTime, $endTime]);
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

    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        ]);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('date', [
            now()->toDateString(),
            now()->addDays($days)->toDateString()
        ]);
    }

    public function scopeOrderedByDateTime($query)
    {
        return $query->orderBy('date')->orderBy('time');
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

    public function getDateTimeAttribute()
    {
        return $this->date->format('Y-m-d') . ' ' . $this->time->format('H:i:s');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('l, F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->time->format('g:i A');
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->formatted_date . ' at ' . $this->formatted_time;
    }

    public function isToday()
    {
        return $this->date->isToday();
    }

    public function isPast()
    {
        return $this->date->isPast() || ($this->date->isToday() && $this->time->isPast());
    }

    public function isFuture()
    {
        return $this->date->isFuture() || ($this->date->isToday() && $this->time->isFuture());
    }

    public function isThisWeek()
    {
        return $this->date->between(
            now()->startOfWeek(),
            now()->endOfWeek()
        );
    }

    public function isThisMonth()
    {
        return $this->date->between(
            now()->startOfMonth(),
            now()->endOfMonth()
        );
    }

    // Helper methods for statistics
    public static function getScheduleCountByLocation()
    {
        return self::selectRaw('location, COUNT(*) as count')
                   ->groupBy('location')
                   ->pluck('count', 'location');
    }

    public static function getScheduleCountBySemester()
    {
        return self::selectRaw('semester, COUNT(*) as count')
                   ->groupBy('semester')
                   ->pluck('count', 'semester');
    }

    public static function getScheduleCountByCourse($courseId = null)
    {
        $query = self::selectRaw('course_id, COUNT(*) as count')
                     ->groupBy('course_id');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->pluck('count', 'course_id');
    }

    public static function getTodaySchedule($location = null)
    {
        $query = self::with(['course', 'intake'])
                     ->today()
                     ->orderedByDateTime();

        if ($location) {
            $query->byLocation($location);
        }

        return $query->get();
    }

    public static function getUpcomingSchedule($days = 7, $location = null)
    {
        $query = self::with(['course', 'intake'])
                     ->upcoming($days)
                     ->orderedByDateTime();

        if ($location) {
            $query->byLocation($location);
        }

        return $query->get();
    }
} 