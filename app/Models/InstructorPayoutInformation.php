<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorPayoutInformation extends Model
{
    protected $fillable = [
        'user_id',
        'gateway_id',
        'account_name',
        'account_email',
        'bank_name',
        'account_number',
        'routing_number',
        'other_details',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(InstructorPaymentGateway::class, 'gateway_id');
    }
}
