<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LatestCourseSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'limit_items',
    ];
}
