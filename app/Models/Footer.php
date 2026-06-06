<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $fillable = [
        'description',
        'contact_title',
        'email',
        'phone',
        'address',
        'bottom_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}