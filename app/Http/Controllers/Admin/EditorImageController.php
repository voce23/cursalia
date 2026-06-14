<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Subida de imágenes desde el editor enriquecido (botón "Imagen").
 * Guarda en storage/app/public/editor y devuelve la URL pública.
 */
class EditorImageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // máx 5 MB
        ], [
            'image.image' => 'El archivo debe ser una imagen (jpg, png, webp, gif).',
            'image.max' => 'La imagen es demasiado grande (máx. 5 MB).',
        ]);

        $file = $request->file('image');
        $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'png');
        $path = 'editor/'.Str::random(16).'.'.$ext;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return response()->json(['url' => Storage::url($path)]);
    }
}
