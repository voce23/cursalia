<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderNavigationLink;
use App\View\Composers\BrandingComposer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD del menú primario (enlaces del header).
 *
 * Una vista única con drag-to-reorder, edit inline y toggle activo/inactivo.
 * Al guardar invalida el cache de branding → cambio visible al instante.
 */
class NavigationController extends Controller
{
    public function edit(): View
    {
        $links = HeaderNavigationLink::query()->orderBy('sort_order')->get();

        return view('admin.navigation.edit', compact('links'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:255'],
            'open_in_new_tab' => ['nullable', 'boolean'],
        ]);

        $max = (int) HeaderNavigationLink::max('sort_order');
        HeaderNavigationLink::create([
            'title' => $data['title'],
            'url' => $data['url'],
            'open_in_new_tab' => (bool) ($data['open_in_new_tab'] ?? false),
            'is_active' => true,
            'sort_order' => $max + 1,
        ]);

        BrandingComposer::flushCache();

        return back()->with('success', 'Enlace "'.$data['title'].'" añadido al menú.');
    }

    public function update(Request $request, HeaderNavigationLink $link): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:255'],
            'open_in_new_tab' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $link->update([
            'title' => $data['title'],
            'url' => $data['url'],
            'open_in_new_tab' => (bool) ($data['open_in_new_tab'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        BrandingComposer::flushCache();

        return back()->with('success', 'Enlace actualizado.');
    }

    public function destroy(HeaderNavigationLink $link): RedirectResponse
    {
        $title = $link->title;
        $link->delete();
        BrandingComposer::flushCache();

        return back()->with('success', 'Enlace "'.$title.'" eliminado.');
    }

    /** Reordenar enlaces vía AJAX (drag & drop). */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:header_navigation_links,id'],
        ]);

        foreach ($data['order'] as $position => $id) {
            HeaderNavigationLink::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        BrandingComposer::flushCache();

        return response()->json(['ok' => true, 'message' => 'Orden actualizado.']);
    }

    /** Toggle on/off rápido vía AJAX. */
    public function toggle(HeaderNavigationLink $link): JsonResponse
    {
        $link->is_active = ! $link->is_active;
        $link->save();
        BrandingComposer::flushCache();

        return response()->json(['ok' => true, 'is_active' => $link->is_active]);
    }
}
