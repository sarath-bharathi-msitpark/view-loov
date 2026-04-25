<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdleTimeOut extends Model
{
    
    protected $table = 'idle_time_outs';
    
    protected $fillable = [
        'user_id',
        'attendance_id',
        'start_time_and_date',
        'end_time_and_date',
        'duration'
    
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

}
