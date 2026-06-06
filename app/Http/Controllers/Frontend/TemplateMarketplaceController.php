<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\TemplateWaitlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Marketplace público de plantillas Cursalia (Sprint 7.9).
 *
 * FASE 1: las plantillas gratuitas se descargan directamente; las de pago
 * registran al interesado en una lista de espera (waitlist).
 * FASE 2: la waitlist se reemplazará por checkout Stripe/PayPal.
 */
class TemplateMarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();
        $price        = $request->string('price')->toString();   // free | paid
        $sort         = $request->string('sort')->toString();

        $templates = Template::query()
            ->published()
            ->with('category:id,name,slug,color,icon')
            ->when($categorySlug, fn ($q) =>
                $q->whereHas('category', fn ($x) => $x->where('slug', $categorySlug)))
            ->when($price === 'free', fn ($q) => $q->free())
            ->when($price === 'paid', fn ($q) => $q->paid())
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(fn ($x) =>
                    $x->where('title', 'like', "%{$s}%")
                      ->orWhere('headline', 'like', "%{$s}%"));
            })
            ->when($sort === 'price_low',  fn ($q) => $q->orderByRaw('COALESCE(discount, price) asc'))
            ->when($sort === 'price_high', fn ($q) => $q->orderByRaw('COALESCE(discount, price) desc'))
            ->when($sort === 'popular',    fn ($q) => $q->orderByDesc('sales_count')->orderByDesc('downloads_count'))
            ->when(! in_array($sort, ['price_low','price_high','popular'], true),
                fn ($q) => $q->orderByDesc('is_featured')->orderBy('sort_order'))
            ->paginate(12)
            ->withQueryString();

        $categories = TemplateCategory::query()
            ->where('is_active', true)
            ->withCount(['templates' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $featured = Template::query()
            ->published()
            ->featured()
            ->with('category:id,name,slug,color')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('frontend.templates.index', compact('templates', 'categories', 'featured'));
    }

    public function show(string $slug): View
    {
        $template = Template::query()
            ->published()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $related = Template::query()
            ->published()
            ->where('id', '!=', $template->id)
            ->when($template->template_category_id, fn ($q) =>
                $q->where('template_category_id', $template->template_category_id))
            ->with('category:id,name,slug,color')
            ->orderByDesc('is_featured')
            ->take(3)
            ->get();

        return view('frontend.templates.show', compact('template', 'related'));
    }

    /** Registro en waitlist (plantillas de pago en FASE 1). */
    public function joinWaitlist(Request $request, string $slug): RedirectResponse
    {
        $template = Template::published()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'email'          => ['required', 'email', 'max:255'],
            'name'           => ['nullable', 'string', 'max:120'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'captcha_token'  => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! \App\Helpers\MathCaptcha::verify($data['captcha_token'], $data['captcha_answer'])) {
            return back()->withErrors(['captcha_answer' => 'La respuesta no coincide. ¿Eres humano? 😊 Inténtalo de nuevo.'])->withInput();
        }

        TemplateWaitlist::updateOrCreate(
            ['template_id' => $template->id, 'email' => strtolower($data['email'])],
            ['name' => $data['name'] ?? null, 'notes' => $data['notes'] ?? null, 'ip' => $request->ip()]
        );

        return back()->with('success', '¡Estás en la lista! Te avisamos en cuanto «'.$template->title.'» esté disponible.');
    }

    /** Descarga directa de plantillas gratuitas + tracking. */
    public function download(string $slug): RedirectResponse
    {
        $template = Template::published()->free()->where('slug', $slug)->firstOrFail();

        $template->increment('downloads_count');

        if ($template->download_url) {
            return redirect()->away($template->download_url);
        }
        return back()->with('success', 'Pronto activaremos la descarga directa. ¡Gracias por tu interés!');
    }
}
