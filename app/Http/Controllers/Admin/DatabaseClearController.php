<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DatabaseClearController extends Controller
{
    public function __construct()
    {
        // SEGURIDAD CRÍTICA: este controller borra TODA la BD.
        // Solo se puede ejecutar en entornos de desarrollo. En producción
        // devuelve 403 incluso aunque el middleware admin se haya colado.
        // Si necesitas usarlo en staging, añade STAGING a la lista whitelist.
        abort_unless(
            in_array(app()->environment(), ['local', 'development', 'testing']),
            403,
            'Database clear está deshabilitado en este entorno por seguridad.'
        );
    }

    public function index(): View
    {
        return view('admin.database-clear.index');
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'confirmation' => ['required', 'in:CLEAR DATABASE'],
        ]);

        try {
            Artisan::call('migrate:fresh', [
                '--seed' => true,
                '--force' => true,
            ]);

            Artisan::call('optimize:clear');

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'La base de datos fue reiniciada y los seeders iniciales se ejecutaron correctamente.',
                'redirect' => route('admin.login'),
            ]);
        } catch (\Throwable $throwable) {
            report($throwable);

            return response()->json([
                'message' => 'No se pudo limpiar la base de datos. Revisa storage/logs/laravel.log para más detalle.',
            ], 500);
        }
    }
}
