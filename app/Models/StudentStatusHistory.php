<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStatusHistory extends Model
{
    use HasFactory;

    // Default table name will be "student_status_histories" (correct)
    protected $fillable = [
        'student_id',
        'from_status',
        'to_status',
        'reason',
        'document',
        'changed_by',
    ];

    public function student()
    {
        // students table uses "student_id" as PK
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function user()
    {
    // users table uses 'user_id' as primary key in this project
    return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }
}
