<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationLogIcon extends Model
{
    protected $table = 'application_log_icon';

    /**
     * @var string[]
     */
    protected $fillable = [
        'application_log_id',
        'image',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;
}
