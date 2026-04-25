<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectTeam extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'project_id',
        'team_id',
        'invited_by',
    ];

    /**
     * @return HasOne
     */
    public function team()
    {
        return $this->hasOne('App\Models\Team', 'id', 'team_id');
    }
}
