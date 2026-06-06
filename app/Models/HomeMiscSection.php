<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeMiscSection extends Model
{
    protected $fillable = [
        'newsletter_title',
        'newsletter_subtitle',
        'instructor_banner_title',
        'instructor_banner_subtitle',
        'instructor_banner_button_text',
        'instructor_banner_button_url',
        'video_section_title',
        'video_url',
    ];
}
