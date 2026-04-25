<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'shift_name','timezone','start_time','end_time','grace_period','max_break_time','week_off','created_by'
    ];
  
}
