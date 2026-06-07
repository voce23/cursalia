<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD admin de artículos del blog · adaptado a Cursalia.
 *
 * Diferencias con el del LMSL13:
 * - Thumbnail OPCIONAL (no obligatorio).
 * - Slug editable (auto-genera del título si vacío).
 * - `Image::read` en lugar de `Image::decode` (Intervention v3).
 * - Mensajes con `back()->with('success')` en lugar de `flash()`.
 * - SVG soportado: se conserva sin recodificar.
 */
class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $items = Blog::query()
            ->with(['category', 'author'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($x) => $x->where('slug', $request->string('category'))))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(fn ($x) => $x->where('title', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = BlogCategory::query()->orderBy('name')->get();

        return view('admin.blogs.index', compact('items', 'categories'));
    }

    public function create(): View
    {
        $categories = BlogCategory::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.blogs.form', [
            'blog'       => new Blog(['status' => 'draft']),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);
        $data['admin_id'] = auth('admin')->id();
        $data['slug']     = $this->uniqueSlug($data['slug'] ?: $data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->saveImage($request->file('thumbnail'));
        }
        if ($request->hasFile('og_image_custom')) {
            $data['og_image_custom'] = $this->saveImage($request->file('og_image_custom'));
        }

        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        Blog::create($data);

        return to_route('admin.blogs.index')->with('success', 'Artículo del blog creado.');
    }

    public function edit(Blog $blog): View
    {
        $categories = BlogCategory::query()
            ->where('status', true)
            ->orWhere('id', $blog->blog_category_id)
            ->orderBy('name')
            ->get();

        return view('admin.blogs.form', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $this->validateRequest($request, $blog->id);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $blog->id);

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $data['thumbnail'] = $this->saveImage($request->file('thumbnail'));
        }
        if ($request->hasFile('og_image_custom')) {
            if ($blog->og_image_custom) {
                Storage::disk('public')->delete($blog->og_image_custom);
            }
            $data['og_image_custom'] = $this->saveImage($request->file('og_image_custom'));
        }

        $data['published_at'] = $data['status'] === 'published'
            ? ($blog->published_at ?? now())
            : null;

        $blog->update($data);

        return to_route('admin.blogs.index')->with('success', 'Artículo actualizado.');
    }

    public function destroy(Blog $blog): JsonResponse
    {
        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }
        $blog->delete();

        return response()->json(['message' => 'Artículo eliminado.']);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255'],
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'summary'          => ['nullable', 'string', 'max:500'],
            'content'          => ['required', 'string'],
            // mimes + mimetypes: la primera valida la extensión, la segunda el tipo MIME REAL del archivo (defensa en profundidad contra `archivo.php.jpg`).
            'thumbnail'        => ['nullable', 'file', 'mimes:jpeg,png,webp,svg', 'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml', 'max:4096'],
            'status'           => ['required', 'in:draft,published'],
            // ─── SEO ────────────────────────────────────────────────────
            'meta_title'       => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:180'],
            'og_image_custom'  => ['nullable', 'file', 'mimes:jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'faq'              => ['nullable', 'array'],
            'faq.*.q'          => ['nullable', 'string', 'max:255'],
            'faq.*.a'          => ['nullable', 'string', 'max:1500'],
        ]);

        // Limpiar FAQ: descartar filas vacías.
        if (!empty($data['faq'])) {
            $data['faq'] = array_values(array_filter($data['faq'], fn ($item) =>
                !empty(trim($item['q'] ?? '')) && !empty(trim($item['a'] ?? ''))
            ));
            if (empty($data['faq'])) {
                $data['faq'] = null;
            }
        }

        return $data;
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        // Si ya viene un slug "limpio" (con guiones), lo respetamos tal cual;
        // si no, lo generamos del título. Esto permite al admin usar
        // slugs específicos como "lec-02-mi-titulo".
        $slug = preg_match('/^[a-z0-9-]+$/', $base) ? $base : Str::slug($base);
        $original = $slug;
        $i = 1;
        while (
            Blog::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.$i++;
        }
        return $slug;
    }

    private function saveImage(\Illuminate\Http\UploadedFile $file): string
    {
        // Delega al ImageOptimizer: genera WebP + AVIF + responsive (480/800/1200)
        // automáticamente, o minifica si es SVG. El nombre devuelto es el del
        // archivo "principal" que guardamos en blogs.thumbnail.
        return app(ImageOptimizer::class)->processUpload(
            file: $file,
            folder: 'blog',
            targetWidth: 1200,
            targetHeight: 675,
            cover: true,
            responsiveSizes: [480, 800, 1200],
        );
    }
}
