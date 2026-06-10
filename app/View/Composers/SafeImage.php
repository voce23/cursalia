<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Storage;

/**
 * Helper estático para mostrar imágenes de forma segura.
 *
 * Si el path apunta a un archivo que no existe en disco, devuelve null para
 * que la vista pueda mostrar su placeholder (iniciales, gradient, etc.).
 *
 * Uso:
 *
 *   @if ($url = \App\View\Composers\SafeImage::url($user->image))
 *       <img src="{{ $url }}" alt="{{ $user->name }}">
 *
 *   @else
 *       <span class="iniciales">{{ ... }}</span>
 *
 *   @endif
 */
class SafeImage
{
    /** Devuelve la URL pública si el archivo existe, o null si no. */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        // Si ya es una URL absoluta (http/https), confiamos.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.$path);
    }
}
