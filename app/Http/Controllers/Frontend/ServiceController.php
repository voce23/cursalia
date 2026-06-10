<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\MathCaptcha;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Página pública de Servicios & Asesoría.
 *
 * El producto Cursalia FREE es gratis; los servicios humanos (asesoría,
 * instalación, personalización) sí se cobran. Aquí se listan y se reciben
 * pedidos por formulario que persisten en BD para que el admin los gestione.
 */
class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $preselect = $request->string('service')->toString() ?: null;

        return view('frontend.services.index', compact('services', 'preselect'));
    }

    public function storeRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'contact_preference' => ['required', 'in:email,whatsapp,both'],
            'budget' => ['nullable', 'string', 'max:32'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::verify($data['captcha_token'], $data['captcha_answer'])) {
            return back()->withErrors(['captcha_answer' => 'La respuesta no coincide. ¿Eres humano? 😊 Inténtalo de nuevo.'])->withInput();
        }

        // Quitar los campos del captcha antes de guardar en BD
        unset($data['captcha_token'], $data['captcha_answer']);
        $data['ip'] = $request->ip();
        ServiceRequest::create($data);

        return back()
            ->with('success', '¡Solicitud recibida! Te responderemos en menos de 24 horas hábiles.')
            ->withFragment('formulario');
    }
}
