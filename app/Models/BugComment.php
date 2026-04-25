<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugComment extends Model
{
    protected $fillable = [
        'comment',
        'bug_id',
        'created_by',
        'user_type',
    ];

    /**
     * Relationship: The user who created the comment
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
}
