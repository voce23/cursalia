<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonComment extends Model
{
    protected $fillable = [
        'lesson_id',
        'name',
        'email',
        'comment',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseChapterLesson::class, 'lesson_id');
    }
}
