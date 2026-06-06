<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    /** Total de lecciones de la Fase 1 del curso (configuración de negocio). */
    public const COURSE_FREE_TOTAL = 14;
    public const COURSE_CATEGORY_SLUG = 'curso-cursalia';

    protected $fillable = [
        'admin_id',
        'blog_category_id',
        'title',
        'slug',
        'thumbnail',
        'summary',
        'content',
        'status',
        'published_at',
        // SEO
        'meta_title',
        'meta_description',
        'og_image_custom',
        'faq',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'faq'          => 'array', // [{q, a}, ...]
    ];

    // ── Accessors SEO ─────────────────────────────────────────────────────────

    /**
     * Título para Google.
     *   - Si hay meta_title manual: lo respetamos tal cual.
     *   - Si no: usamos el title del post sin recortar (Google ya recorta a ~60 según ancho en píxeles).
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * Descripción para Google.
     *   - Si hay meta_description manual: la respetamos.
     *   - Si no: summary > primeros 155 char del contenido (recorte limpio en palabras).
     */
    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }
        $base = $this->summary ?: strip_tags($this->content);
        $base = trim(preg_replace('/\s+/', ' ', $base));
        return \Illuminate\Support\Str::limit($base, 155, '');
    }

    /** URL absoluta de la imagen OG/Twitter del post. */
    public function getOgImageUrlAttribute(): ?string
    {
        $path = $this->og_image_custom ?: $this->thumbnail;
        return $path ? asset('storage/'.$path) : null;
    }

    // ── Relaciones ──────────────────────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    // ── Curso: navegación entre lecciones ──────────────────────────────────

    /**
     * Si el slug empieza por "lec-XX-", devuelve el número de lección. null si no.
     */
    public function getLessonNumber(): ?int
    {
        if (! preg_match('/^lec-(\d{1,3})-/', $this->slug, $m)) {
            return null;
        }
        return (int) $m[1];
    }

    /** ¿Es una lección del curso Cursalia (Fase 1 o 2)? */
    public function isCourseLesson(): bool
    {
        return $this->category && $this->category->slug === self::COURSE_CATEGORY_SLUG
            && $this->getLessonNumber() !== null;
    }

    /** Encuentra la siguiente lección publicada del curso (por número en el slug). */
    public function nextLesson(): ?Blog
    {
        $n = $this->getLessonNumber();
        if ($n === null) return null;

        return static::query()
            ->where('blog_category_id', $this->blog_category_id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('slug', 'like', sprintf('lec-%02d-%%', $n + 1))
            ->first();
    }

    /** Encuentra la lección anterior publicada del curso. */
    public function previousLesson(): ?Blog
    {
        $n = $this->getLessonNumber();
        if ($n === null || $n === 0) return null;

        return static::query()
            ->where('blog_category_id', $this->blog_category_id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('slug', 'like', sprintf('lec-%02d-%%', $n - 1))
            ->first();
    }
}
