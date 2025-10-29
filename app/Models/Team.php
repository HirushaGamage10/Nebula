<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'profile_pic', 'p_no', 'link1', 'link2', 'phase_id',
    ];

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function roles()
    {
        return $this->hasMany(TeamRole::class, 'team_id');
    }
}
