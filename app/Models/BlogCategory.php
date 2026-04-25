<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'short_description', 'status',
        'meta_title', 'meta_description', 'meta_keywords',
        'created_by'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
