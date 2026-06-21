<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivationKey;
use App\Helpers\Pro;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Activación de Cursalia PRO (compartida por TODOS los complementos PRO).
 * Modelo Divi: una sola llave PRO desbloquea todo.
 */
class ProController extends Controller
{
    public function activate(Request $request)
    {
        $request->validate(['pro_key' => 'required|string|max:200']);

        // Case-sensitive (base64url): no se hace strtoupper.
        $key = trim((string) $request->input('pro_key'));
        if (! ActivationKey::validate($key, Pro::PREFIX)) {
            return back()->withErrors(['pro_key' => 'La llave PRO no es válida. Consíguela en cursalia.org.']);
        }

        Pro::store($key);
        flash()->success('¡Cursalia PRO activado! Ya tienes desbloqueados todos los complementos PRO.');

        return back();
    }
}
