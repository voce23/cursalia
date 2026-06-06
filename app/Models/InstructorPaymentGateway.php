<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorPaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'type',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Etiqueta legible del tipo de pasarela.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'paypal'         => 'PayPal Personal',
            'bank_transfer'  => 'Transferencia Bancaria',
            'stripe_connect' => 'Stripe Connect',
            default          => 'Otro',
        };
    }

    public function payoutInformations(): HasMany
    {
        return $this->hasMany(InstructorPayoutInformation::class, 'gateway_id');
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'gateway_id');
    }
}
