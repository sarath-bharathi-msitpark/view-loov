<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beat extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area_id','created_by', 
        'status'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
