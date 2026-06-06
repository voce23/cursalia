<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    protected $fillable = [
        'category_button_text',
        'category_limit',
        'show_search',
        'search_placeholder',
    ];

    protected $casts = [
        'show_search' => 'boolean',
        'category_limit' => 'integer',
    ];
}