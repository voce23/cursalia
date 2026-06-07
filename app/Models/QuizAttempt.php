<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'started_at',
        'completed_at',
        'score',
        'percentage',
        'passed',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'passed' => 'boolean',
            'percentage' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    /** Duración del intento en segundos */
    public function durationSeconds(): ?int
    {
        if ($this->completed_at && $this->started_at) {
            return $this->completed_at->diffInSeconds($this->started_at);
        }
        return null;
    }

    /** Estado legible del resultado */
    public function resultStatus(): string
    {
        return $this->passed ? '✅ Aprobado' : '❌ No aprobado';
    }
}
