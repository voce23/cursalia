<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Suscripción al newsletter del footer.
 *
 * - Idempotente: si el email ya existe, no duplica (firstOrCreate).
 * - Normaliza el email a minúsculas.
 * - Throttle en routes/web.php para evitar abuso (6/min).
 */
class NewsletterSubscribeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => strtolower(trim($request->email))],
            ['is_active' => true]
        );

        return back()->with('status', '¡Gracias por suscribirte! 💚');
    }
}
