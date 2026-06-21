<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Pro;
use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;

/**
 * Complemento PRO: optimizar las imágenes del LMS a WebP (sitio más rápido).
 * Bloqueado tras la llave PRO (compartida con los demás complementos).
 */
class ImageToolController extends Controller
{
    public function index()
    {
        return view('admin.image-tools.index', ['proActive' => Pro::isActive()]);
    }

    /** Convierte las imágenes de contenido a WebP. */
    public function optimize()
    {
        if (! Pro::isActive()) {
            return redirect()->route('admin.image-tools.index');
        }

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $r = ImageOptimizer::optimizeAll();
        $mb = number_format($r['saved'] / 1048576, 1);

        if ($r['count'] === 0 && $r['errors'] === 0) {
            flash()->success('Todo al día: no había imágenes que optimizar (o ya están en WebP).');
        } else {
            flash()->success("Listo: {$r['count']} imágenes convertidas a WebP · {$mb} MB ahorrados".($r['errors'] ? " · {$r['errors']} con error" : '').'.');
        }

        return back();
    }
}
