<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'role'];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
