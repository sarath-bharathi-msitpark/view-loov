<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'status',
        'meta_title', 'meta_description', 'meta_keywords', 'created_by'
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

}
