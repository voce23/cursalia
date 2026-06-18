<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOrder extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'instructor_id', 'method',
        'amount', 'currency', 'status', 'proof_path', 'transaction_id', 'reference',
    ];

    public const METHODS = [
        'stripe' => 'Tarjeta (Stripe)',
        'paypal' => 'PayPal',
        'qr' => 'QR',
        'transfer' => 'Transferencia bancaria',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isManual(): bool
    {
        return in_array($this->method, ['qr', 'transfer'], true);
    }
}
