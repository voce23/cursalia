<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'slug', 'title', 'headline', 'description', 'icon', 'color',
        'price', 'currency', 'price_suffix', 'is_free',
        'features', 'badge_text',
        'cta_text', 'cta_url',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'features'    => 'array',
        'is_free'     => 'boolean',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'price'       => 'decimal:2',
        'sort_order'  => 'integer',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
