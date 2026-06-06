<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopBar extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'offer_text',
        'offer_url',
        'background_color',
        'text_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}