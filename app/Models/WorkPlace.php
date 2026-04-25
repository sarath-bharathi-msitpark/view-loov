<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlace extends Model
{
    use HasFactory;

    protected $table = 'work_places';

      protected $fillable = [
        'user_id',
        'workplace_max_hours_for_absent',
'workplace_min_hours_for_half_day',
'workplace_min_hours_for_full_day',
       

    ];
}
