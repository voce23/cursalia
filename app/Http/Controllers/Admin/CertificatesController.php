<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Pro;
use App\Http\Controllers\Controller;

/**
 * Complemento PRO: Certificados de finalización. La configuración real (qué
 * curso emite certificado) vive en cada curso; aquí solo se activa/explica el
 * complemento, bloqueado tras la llave PRO.
 */
class CertificatesController extends Controller
{
    public function index()
    {
        return view('admin.certificates.index', ['proActive' => Pro::isActive()]);
    }
}
