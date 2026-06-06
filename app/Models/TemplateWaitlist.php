<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateWaitlist extends Model
{
    protected $fillable = [
        'template_id', 'email', 'name', 'notes', 'ip',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
