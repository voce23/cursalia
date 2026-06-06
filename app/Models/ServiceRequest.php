<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'service_id', 'name', 'email', 'whatsapp', 'contact_preference',
        'budget', 'subject', 'message', 'status', 'admin_notes', 'ip',
    ];

    public const STATUSES = [
        'new'         => 'Nuevo',
        'contacted'   => 'Contactado',
        'in_progress' => 'En proceso',
        'closed'      => 'Cerrado',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
