<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    use HasFactory;

    protected $table = 'student_exams';
    protected $primaryKey = 'exam_id';

    protected $fillable = [
        'student_id',
        'ol_exam_type',
        'ol_exam_year',
        'ol_index_no',
        'ol_certificate',
        'ol_exam_subjects',
        'al_exam_type',
        'al_exam_year',
        'al_exam_stream',
        'al_index_no',
        'z_score_value',
        'al_certificate',
        'al_exam_subjects',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'exam_id' => 'int',
        'student_id' => 'int',
        'ol_exam_year' => 'string',
        'al_exam_year' => 'string',
        'z_score_value' => 'string',
        'ol_exam_subjects' => 'array',
        'al_exam_subjects' => 'array',
    ];

    // Constants for exam types
    const EXAM_TYPE_LOCAL = 'Local';
    const EXAM_TYPE_LONDON_CAMBRIDGE = 'London Cambridge';
    const EXAM_TYPE_LONDON_EDEXCEL = 'London Edexcel';
    const EXAM_TYPE_OTHER = 'Other';

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scopes
    public function scopeByOlExamType($query, $examType)
    {
        return $query->where('ol_exam_type', $examType);
    }

    public function scopeByAlExamType($query, $examType)
    {
        return $query->where('al_exam_type', $examType);
    }

    public function scopeByExamType($query, $examType)
    {
        return $query->where('ol_exam_type', $examType)
                    ->orWhere('al_exam_type', $examType);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('ol_exam_year', $year)
                    ->orWhere('al_exam_year', $year);
    }

    public function scopeByOlYear($query, $year)
    {
        return $query->where('ol_exam_year', $year);
    }

    public function scopeByAlYear($query, $year)
    {
        return $query->where('al_exam_year', $year);
    }

    public function scopeByStream($query, $stream)
    {
        return $query->where('al_exam_stream', $stream);
    }

    public function scopeWithZScore($query)
    {
        return $query->whereNotNull('z_score_value')
                    ->where('z_score_value', '!=', '');
    }

    public function scopeWithOlCertificate($query)
    {
        return $query->whereNotNull('ol_certificate')
                    ->where('ol_certificate', '!=', '');
    }

    public function scopeWithAlCertificate($query)
    {
        return $query->whereNotNull('al_certificate')
                    ->where('al_certificate', '!=', '');
    }

    public function scopeLocal($query)
    {
        return $query->where('ol_exam_type', self::EXAM_TYPE_LOCAL)
                    ->orWhere('al_exam_type', self::EXAM_TYPE_LOCAL);
    }

    public function scopeLondonCambridge($query)
    {
        return $query->where('ol_exam_type', self::EXAM_TYPE_LONDON_CAMBRIDGE)
                    ->orWhere('al_exam_type', self::EXAM_TYPE_LONDON_CAMBRIDGE);
    }

    public function scopeLondonEdexcel($query)
    {
        return $query->where('ol_exam_type', self::EXAM_TYPE_LONDON_EDEXCEL)
                    ->orWhere('al_exam_type', self::EXAM_TYPE_LONDON_EDEXCEL);
    }

    public function scopeOther($query)
    {
        return $query->where('ol_exam_type', self::EXAM_TYPE_OTHER)
                    ->orWhere('al_exam_type', self::EXAM_TYPE_OTHER);
    }

    // Static methods
    public static function getExamTypes()
    {
        return [
            self::EXAM_TYPE_LOCAL,
            self::EXAM_TYPE_LONDON_CAMBRIDGE,
            self::EXAM_TYPE_LONDON_EDEXCEL,
            self::EXAM_TYPE_OTHER
        ];
    }

    // Accessors
    public function getOlExamYearFormattedAttribute()
    {
        return $this->ol_exam_year ?: 'N/A';
    }

    public function getAlExamYearFormattedAttribute()
    {
        return $this->al_exam_year ?: 'N/A';
    }

    public function getZScoreFormattedAttribute()
    {
        return $this->z_score_value ?: 'N/A';
    }

    public function getOlExamTypeDisplayAttribute()
    {
        return $this->ol_exam_type ?: 'N/A';
    }

    public function getAlExamTypeDisplayAttribute()
    {
        return $this->al_exam_type ?: 'N/A';
    }

    public function getOlIndexNoDisplayAttribute()
    {
        return $this->ol_index_no ?: 'N/A';
    }

    public function getAlIndexNoDisplayAttribute()
    {
        return $this->al_index_no ?: 'N/A';
    }

    public function getAlStreamDisplayAttribute()
    {
        return $this->al_exam_stream ?: 'N/A';
    }

    public function getHasOlCertificateAttribute()
    {
        return !empty($this->ol_certificate);
    }

    public function getHasAlCertificateAttribute()
    {
        return !empty($this->al_certificate);
    }

    public function getHasZScoreAttribute()
    {
        return !empty($this->z_score_value);
    }

    public function getHasOlSubjectsAttribute()
    {
        return !empty($this->ol_exam_subjects);
    }

    public function getHasAlSubjectsAttribute()
    {
        return !empty($this->al_exam_subjects);
    }

    // Methods
    public function isLocalExam()
    {
        return $this->ol_exam_type === self::EXAM_TYPE_LOCAL || 
               $this->al_exam_type === self::EXAM_TYPE_LOCAL;
    }

    public function isLondonCambridgeExam()
    {
        return $this->ol_exam_type === self::EXAM_TYPE_LONDON_CAMBRIDGE || 
               $this->al_exam_type === self::EXAM_TYPE_LONDON_CAMBRIDGE;
    }

    public function isLondonEdexcelExam()
    {
        return $this->ol_exam_type === self::EXAM_TYPE_LONDON_EDEXCEL || 
               $this->al_exam_type === self::EXAM_TYPE_LONDON_EDEXCEL;
    }

    public function isOtherExam()
    {
        return $this->ol_exam_type === self::EXAM_TYPE_OTHER || 
               $this->al_exam_type === self::EXAM_TYPE_OTHER;
    }

    public function hasOlExam()
    {
        return !empty($this->ol_exam_type);
    }

    public function hasAlExam()
    {
        return !empty($this->al_exam_type);
    }

    public function getOlSubjectsArray()
    {
        return $this->ol_exam_subjects ?: [];
    }

    public function getAlSubjectsArray()
    {
        return $this->al_exam_subjects ?: [];
    }

    public function getOlSubjectsCount()
    {
        return count($this->getOlSubjectsArray());
    }

    public function getAlSubjectsCount()
    {
        return count($this->getAlSubjectsArray());
    }

    public function getExamSummary()
    {
        $summary = [];
        
        if ($this->hasOlExam()) {
            $summary[] = "OL: {$this->ol_exam_type} ({$this->ol_exam_year})";
        }
        
        if ($this->hasAlExam()) {
            $summary[] = "AL: {$this->al_exam_type} ({$this->al_exam_year})";
            if ($this->al_exam_stream) {
                $summary[] = "Stream: {$this->al_exam_stream}";
            }
        }
        
        return implode(', ', $summary);
    }
} 