<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }
}
