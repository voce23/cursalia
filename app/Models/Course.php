<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'course_level_id',
        'course_language_id',
        'title',
        'slug',
        'seo_description',
        'thumbnail',
        'demo_video_storage',
        'demo_video_source',
        'description',
        'price',
        'discount',
        'duration',
        'certificate',
        'qna',
        'message_for_reviewer',
        'is_approved',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'certificate' => 'boolean',
            'qna' => 'boolean',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(CourseLevel::class, 'course_level_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(CourseLanguage::class, 'course_language_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(CourseChapter::class)->orderBy('order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseChapterLesson::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }
}
