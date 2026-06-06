<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'template_category_id',
        'title', 'slug', 'headline', 'description',
        'thumbnail', 'gallery',
        'price', 'discount', 'is_free',
        'demo_url', 'download_url', 'version',
        'tech_stack', 'features',
        'status', 'sales_count', 'downloads_count',
        'is_featured', 'sort_order',
    ];

    protected $casts = [
        'gallery'         => 'array',
        'tech_stack'      => 'array',
        'features'        => 'array',
        'price'           => 'decimal:2',
        'discount'        => 'decimal:2',
        'is_free'         => 'boolean',
        'is_featured'     => 'boolean',
        'sales_count'     => 'integer',
        'downloads_count' => 'integer',
        'sort_order'      => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'template_category_id');
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(TemplateWaitlist::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published');
    }

    public function scopeFree(Builder $q): Builder
    {
        return $q->where('is_free', true);
    }

    public function scopePaid(Builder $q): Builder
    {
        return $q->where('is_free', false)->where('price', '>', 0);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    // ── Accessors ──────────────────────────────────────────────────────────
    public function getFinalPriceAttribute(): float
    {
        if ($this->is_free) {
            return 0.0;
        }
        return (float) ($this->discount ?: $this->price);
    }

    public function getHasDiscountAttribute(): bool
    {
        return ! $this->is_free
            && $this->discount !== null
            && (float) $this->discount > 0
            && (float) $this->discount < (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->has_discount) {
            return 0;
        }
        return (int) round((1 - (float) $this->discount / (float) $this->price) * 100);
    }
}
