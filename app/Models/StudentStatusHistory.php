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
        // adjust foreign/local keys if your users PK isn’t "id"
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
}
