<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'phase_id',
        'phase_name',
        'start_date',
        'end_date',
        'supervisors'
    ];

    protected $casts = [
        'supervisors' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'phase_id');
    }
}