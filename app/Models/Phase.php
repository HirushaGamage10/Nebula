<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'phase_id', 'phase_name', 'start_date', 'end_date', 'supervisors',
    ];

    protected $casts = [
        'supervisors' => 'array', // decode JSON automatically
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'phase_id');
    }
}
