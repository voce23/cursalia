<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TemplateImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Importador de plantillas (.json) al catálogo de cursos del LMS.
 * Es el "complemento" que permite descargar una plantilla de nicho y
 * cargarla en el panel sin terminal (Opción 2).
 */
class TemplateImportController extends Controller
{
    public function form(): View
    {
        return view('admin.templates.import');
    }

    public function import(Request $request, TemplateImporter $importer): RedirectResponse
    {
        $request->validate([
            'template' => ['required', 'file', 'max:8192'], // hasta 8 MB
        ], [
            'template.required' => 'Elige el archivo .json de la plantilla.',
            'template.max' => 'El archivo es demasiado grande (máx. 8 MB).',
        ]);

        $file = $request->file('template');

        if (strtolower((string) $file->getClientOriginalExtension()) !== 'json') {
            return back()->with('error', 'El archivo debe tener extensión .json.');
        }

        $data = json_decode((string) file_get_contents($file->getRealPath()), true);

        if (! is_array($data)) {
            return back()->with('error', 'El archivo no es un JSON válido. Descárgalo de nuevo e inténtalo otra vez.');
        }

        try {
            $result = $importer->import($data, $request->boolean('replace_demo'));
        } catch (Throwable $e) {
            return back()->with('error', 'No se pudo importar: '.$e->getMessage());
        }

        return to_route('admin.courses.index')->with(
            'success',
            "Plantilla «{$result['name']}» importada: {$result['courses']} cursos y {$result['lessons']} lecciones. "
            .'Ahora edita cada lección para añadir tus videos.'
        );
    }
}
