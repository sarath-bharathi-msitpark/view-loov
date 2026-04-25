<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'user_activity';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'screenshot',
        'keyboard_action_count',
        'mouse_action_count',
        'capture_date_and_time',
        'requested_by',
        'requested_date_and_time',
        'is_web_cam',
        'is_highlight',
    ];

    /**
     * @return HasMany
     */
    public function applicationLog()
    {
        return $this->hasMany(ApplicationLog::class, 'incident_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo
     */
    public function breakType()
    {
        return $this->belongsTo(BreakType::class, 'break_type_id');
    }
}
