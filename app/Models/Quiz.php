<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'passing_score',
        'shuffle_questions',
        'show_results_immediately',
        'allow_retakes',
        'max_attempts',
        'time_limit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_retakes' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseChapterLesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /** Obtener el mejor intento (puntuación más alta) */
    public function bestAttempt()
    {
        return $this->attempts()->orderByDesc('percentage')->first();
    }

    /** Obtener el intento más reciente */
    public function lastAttempt()
    {
        return $this->attempts()->latest('completed_at')->first();
    }

    /** Total de puntos posibles */
    public function totalPoints(): int
    {
        return $this->questions()->sum('points');
    }
}
