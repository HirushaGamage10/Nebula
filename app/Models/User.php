<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Add this line

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Add HasApiTokens here

    protected $table = 'users'; // Set the table name

    protected $primaryKey = 'user_id'; // Set the primary key

    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'password',
        'user_role',
        'status',        
        'user_profile',
        'user_location',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
