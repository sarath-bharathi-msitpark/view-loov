<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationLog extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'application_log';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'incident_id',
        'application_name',
        'url',
        'screen_time',
        'is_browser'
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = true;

    public function icon()
    {
        return $this->hasOne(ApplicationLogIcon::class, 'application_log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
