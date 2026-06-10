<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'show_at_nav',
        'status',
    ];

    protected $casts = [
        'show_at_nav' => 'boolean',
        'status' => 'boolean',
    ];
}
