<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;
class SemesterModule extends Model
{
    use UserTracking;

    protected $table = 'semester_module';
    public $timestamps = false;

    protected $fillable = [
        'semester_id',
        'module_id',
        'specialization',
        'specializations',
    ];

    protected $casts = [
        'specializations' => 'array',
    ];

    // Relationships
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }
}