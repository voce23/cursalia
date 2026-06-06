<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'form_title',
        'form_subtitle',
        'receiver_email',
        'map_embed_url',
    ];
}
