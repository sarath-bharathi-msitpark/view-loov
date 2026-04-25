<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'timezone',
        'punched_in_at',
        'punched_out_at',
        'working_hours',

    ];
}
