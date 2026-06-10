<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseChapterLesson extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'instructor_id',
        'course_id',
        'chapter_id',
        'file_path',
        'storage',
        'duration',
        'file_type',
        'downloadable',
        'order',
        'is_preview',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'downloadable' => 'boolean',
            'is_preview' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(CourseChapter::class, 'chapter_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
