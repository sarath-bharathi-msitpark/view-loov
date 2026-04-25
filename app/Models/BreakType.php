<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakType extends Model
{
    use HasFactory;

    protected $table = 'break_type';

      protected $fillable = [
        'break_name',
        'maximum_break_time',
         'break_limit_apply',
        'status',
       'created_by'

    ];
}
