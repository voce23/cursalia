<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivationKey;
use App\Helpers\Pro;
use App\Http\Controllers\Controller;
use App\Services\MigradorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Migrador (complemento PRO estilo Duplicator): empaqueta el sitio + entrega
 * un instalador web para revivirlo en otro hosting o clonarlo a otro dominio.
 * Bloqueado tras la llave PRO (modelo Divi: una llave, todos los complementos).
 */
class MigradorController extends Controller
{
    public function __construct(private MigradorService $migrador) {}

    public function index()
    {
        $proActive = Pro::isActive();

        return view('admin.migrador.index', [
            'proActive' => $proActive,
            'packages' => $proActive ? $this->migrador->listPackages() : [],
        ]);
    }

    /** Activa Cursalia PRO con la llave que entrega cursalia.org. */
    public function activate(Request $request)
    {
        $request->validate(['pro_key' => 'required|string|max:200']);

        // Case-sensitive (base64url): no se hace strtoupper.
        $key = trim((string) $request->input('pro_key'));
        if (! ActivationKey::validate($key, Pro::PREFIX)) {
            return back()->withErrors(['pro_key' => 'La llave PRO no es válida. Consíguela en cursalia.org.']);
        }

        Pro::store($key);
        flash()->success('¡Cursalia PRO activado! Ya puedes usar el Migrador.');

        return back();
    }

    /** Botón "Crear paquete de migración". */
    public function build()
    {
        if (! Pro::isActive()) {
            return redirect()->route('admin.migrador.index');
        }

        try {
            $this->migrador->buildPackage();
        } catch (Throwable $e) {
            report($e);
            flash()->error('No se pudo crear el paquete: '.$e->getMessage());

            return back();
        }

        flash()->success('Paquete de migración creado. Descarga el paquete y el instalador.');

        return back();
    }

    /** Descarga un paquete de migración a la PC. */
    public function downloadPackage(string $file): BinaryFileResponse
    {
        abort_unless(Pro::isActive(), 403);
        $path = $this->migrador->resolvePackage($file);
        abort_if($path === null, 404);

        return response()->download($path);
    }

    /** Descarga el instalador web (instalador.php). */
    public function downloadInstaller(): BinaryFileResponse
    {
        abort_unless(Pro::isActive(), 403);
        $path = $this->migrador->installerPath();
        abort_if(! is_file($path), 404);

        return response()->download($path, 'instalador.php', ['Content-Type' => 'text/plain']);
    }

    /** Elimina un paquete del servidor. */
    public function destroyPackage(string $file)
    {
        if (! Pro::isActive()) {
            return redirect()->route('admin.migrador.index');
        }

        $path = $this->migrador->resolvePackage($file);
        if ($path) {
            @unlink($path);
        }
        flash()->success('Paquete eliminado.');

        return back();
    }
}
