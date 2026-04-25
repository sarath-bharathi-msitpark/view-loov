<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'name',
        'description',
        'avg_keyboard_clicks_per_day',
        'avg_mouse_clicks_per_day',
        'excessive_keyboard_typing_per_day',
        'excessive_mouse_clicking_per_day',
        'application_policy',
        'shift_id',
        'is_tracking',
        'is_livestream',
        'is_capturescreenshot',
        'is_screenshot_frequency',
        'is_app_url',
        'is_keyboard_mouse',
        'idle_timeout_popup_reminder_in_minutes',
        'auto_punch_out_threshold',
        'is_portal_access',
        'created_by',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

}
