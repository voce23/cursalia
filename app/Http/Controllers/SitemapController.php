<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Course;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Genera el sitemap.xml dinámico con cache de 1 hora.
 *
 * Incluye:
 *   - Páginas estáticas clave (home, courses, about, blog, etc).
 *   - Hub del curso (/blog?category=curso-cursalia).
 *   - Cursos publicados y aprobados.
 *   - Posts del blog publicados.
 *
 * Cómo invalidar manualmente (después de publicar contenido nuevo y querer
 * que el crawler lo vea YA sin esperar la próxima hora):
 *   php artisan cache:forget cursalia.sitemap
 */
class SitemapController extends Controller
{
    public const CACHE_KEY = 'cursalia.sitemap';
    public const TTL = 3600;

    public function __invoke(): Response
    {
        $xml = Cache::remember(self::CACHE_KEY, self::TTL, fn () => $this->buildXml());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function buildXml(): string
    {
        $urls = collect([
            ['loc' => url('/'),                  'priority' => '1.0', 'freq' => 'daily'],
            ['loc' => url('/courses'),           'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => url('/about'),             'priority' => '0.7', 'freq' => 'monthly'],
            ['loc' => url('/sobre-el-autor'),    'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => url('/contact'),           'priority' => '0.5', 'freq' => 'monthly'],
            ['loc' => url('/blog'),              'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => url('/blog?category=curso-cursalia'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => url('/register'),          'priority' => '0.6', 'freq' => 'yearly'],
            ['loc' => url('/login'),             'priority' => '0.4', 'freq' => 'yearly'],
        ]);

        Course::query()
            ->where('is_approved', 'approved')
            ->where('status', 'active')
            ->select('slug', 'updated_at')
            ->get()
            ->each(fn ($c) => $urls->push([
                'loc'      => url('/courses/'.$c->slug),
                'priority' => '0.8',
                'freq'     => 'weekly',
                'lastmod'  => $c->updated_at?->toAtomString(),
            ]));

        Blog::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->select('slug', 'updated_at')
            ->get()
            ->each(fn ($b) => $urls->push([
                'loc'      => url('/blog/'.$b->slug),
                'priority' => '0.7',
                'freq'     => 'monthly',
                'lastmod'  => $b->updated_at?->toAtomString(),
            ]));

        return view('sitemap', ['urls' => $urls])->render();
    }
}
