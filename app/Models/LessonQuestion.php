<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonQuestion extends Model
{
    protected $fillable = ['lesson_id', 'course_id', 'user_id', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseChapterLesson::class, 'lesson_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LessonQuestionAnswer::class, 'question_id')->with('user:id,name,image,role');
    }
}
