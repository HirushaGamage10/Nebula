<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTracking;

class Discount extends Model
{
    use HasFactory, UserTracking;
    protected $table = 'discounts';

    protected $fillable = [
        'name',
        'type',
        'discount_category',
        'value',
        'status',
        'description',
    ];
}
