<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city_id','created_by', 
        'status'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function beats()
    {
        return $this->hasMany(Beat::class);
    }
}
