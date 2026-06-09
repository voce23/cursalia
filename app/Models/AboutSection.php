<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'about_values',
        'image',
        'button_text',
        'button_url',
    ];
}
