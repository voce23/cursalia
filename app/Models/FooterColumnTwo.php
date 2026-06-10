<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterColumnTwo extends Model
{
    protected $fillable = [
        'title',
        'url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
