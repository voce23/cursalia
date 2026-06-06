<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->withCount('requests')
            ->orderBy('sort_order')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.form', [
            'service' => new Service(['is_free' => false, 'is_active' => true, 'currency' => 'USD', 'color' => '#10B981']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);
        $data['slug'] = Str::slug($data['title']);
        $data['features'] = $this->splitMultiline($request->input('features_raw'));
        Service::create($data);
        return to_route('admin.services.index')->with('success', 'Servicio creado.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validateRequest($request);
        if ($data['title'] !== $service->title) {
            $data['slug'] = Str::slug($data['title']);
        }
        $data['features'] = $this->splitMultiline($request->input('features_raw'));
        $service->update($data);
        return to_route('admin.services.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();
        return response()->json(['message' => 'Servicio eliminado.']);
    }

    // ─── Bandeja de pedidos ─────────────────────────────────────────────────

    public function requests(Request $request): View
    {
        $status = $request->string('status')->toString();

        $requests = ServiceRequest::query()
            ->with('service:id,title,slug,color')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $counts = ServiceRequest::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('admin.services.requests', compact('requests', 'counts'));
    }

    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $request->validate([
            'status'      => ['required', 'in:'.implode(',', array_keys(ServiceRequest::STATUSES))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $serviceRequest->update($request->only(['status', 'admin_notes']));
        return back()->with('success', 'Pedido actualizado.');
    }

    private function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'headline'    => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:80'],
            'color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'price'       => ['required', 'numeric', 'min:0'],
            'currency'    => ['required', 'string', 'max:8'],
            'price_suffix'=> ['nullable', 'string', 'max:40'],
            'is_free'     => ['nullable', 'boolean'],
            'badge_text'  => ['nullable', 'string', 'max:40'],
            'cta_text'    => ['required', 'string', 'max:60'],
            'cta_url'     => ['nullable', 'url', 'max:255'],
            'is_active'   => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_free']     = $request->boolean('is_free');
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        return $data;
    }

    private function splitMultiline(?string $raw): array
    {
        if (! $raw) return [];
        return collect(preg_split("/\r?\n/", trim($raw)))
            ->map(fn ($l) => trim($l))
            ->filter()
            ->values()
            ->all();
    }
}
