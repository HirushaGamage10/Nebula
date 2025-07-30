<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOtherInformation extends Model
{
    use HasFactory;

    protected $table = 'other_information';
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'disciplinary_issues',
        'disciplinary_issue_document',
        'continue_higher_studies',
        'institute',
        'field_of_study',
        'currently_employee',
        'job_title',
        'workplace',
        'other_information',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'continue_higher_studies' => 'boolean',
        'currently_employee' => 'boolean',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Scopes
    public function scopeByHigherStudies($query, $continue = true)
    {
        return $query->where('continue_higher_studies', $continue);
    }

    public function scopeByEmployment($query, $employed = true)
    {
        return $query->where('currently_employee', $employed);
    }

    public function scopeByInstitute($query, $institute)
    {
        return $query->where('institute', 'like', "%{$institute}%");
    }

    public function scopeByFieldOfStudy($query, $field)
    {
        return $query->where('field_of_study', 'like', "%{$field}%");
    }

    public function scopeByJobTitle($query, $jobTitle)
    {
        return $query->where('job_title', 'like', "%{$jobTitle}%");
    }

    public function scopeByWorkplace($query, $workplace)
    {
        return $query->where('workplace', 'like', "%{$workplace}%");
    }

    public function scopeWithDisciplinaryIssues($query)
    {
        return $query->whereNotNull('disciplinary_issues')
                    ->where('disciplinary_issues', '!=', '');
    }

    public function scopeWithoutDisciplinaryIssues($query)
    {
        return $query->whereNull('disciplinary_issues')
                    ->orWhere('disciplinary_issues', '');
    }

    // Accessors
    public function getContinueHigherStudiesTextAttribute()
    {
        return $this->continue_higher_studies ? 'Yes' : 'No';
    }

    public function getCurrentlyEmployeeTextAttribute()
    {
        return $this->currently_employee ? 'Yes' : 'No';
    }

    public function getHasDisciplinaryIssuesAttribute()
    {
        return !empty($this->disciplinary_issues);
    }

    public function getDisciplinaryIssuesTextAttribute()
    {
        return $this->has_disciplinary_issues ? 'Yes' : 'No';
    }

    // Methods
    public function isContinuingHigherStudies()
    {
        return $this->continue_higher_studies;
    }

    public function isCurrentlyEmployed()
    {
        return $this->currently_employee;
    }

    public function hasDisciplinaryIssues()
    {
        return $this->has_disciplinary_issues;
    }

    public function getHigherStudiesInfo()
    {
        if (!$this->continue_higher_studies) {
            return 'Not continuing higher studies';
        }

        $info = [];
        if ($this->institute) {
            $info[] = "Institute: {$this->institute}";
        }
        if ($this->field_of_study) {
            $info[] = "Field: {$this->field_of_study}";
        }

        return empty($info) ? 'Continuing higher studies (details not specified)' : implode(', ', $info);
    }

    public function getEmploymentInfo()
    {
        if (!$this->currently_employee) {
            return 'Not currently employed';
        }

        $info = [];
        if ($this->job_title) {
            $info[] = "Job Title: {$this->job_title}";
        }
        if ($this->workplace) {
            $info[] = "Workplace: {$this->workplace}";
        }

        return empty($info) ? 'Currently employed (details not specified)' : implode(', ', $info);
    }
}