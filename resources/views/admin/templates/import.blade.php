@extends('layouts.admin')

@section('title', 'Importar plantilla')
@section('page-title', 'Importar plantilla')
@section('page-subtitle', 'Carga una plantilla de cursos (.json) y se crea el contenido solo')

@section('content')

<div class="max-w-2xl">

    @if (session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 text-sm">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @error('template')
        <div class="mb-5 px-4 py-3 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 text-sm">{{ $message }}</div>
    @enderror

    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-7">
        <div class="flex items-center gap-3 mb-2">
            <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-file-import text-lg"></i>
            </span>
            <div>
                <h2 class="font-display font-extrabold text-lg text-ink-900">Sube tu plantilla</h2>
                <p class="text-sm text-ink-500">Archivo <code class="px-1.5 py-0.5 rounded bg-cream-2 text-ink-700">.json</code> descargado del catálogo de plantillas.</p>
            </div>
        </div>

        <form action="{{ route('admin.templates.import') }}" method="POST" enctype="multipart/form-data" class="mt-5">
            @csrf
            <label class="block">
                <span class="sr-only">Archivo de plantilla</span>
                <input type="file" name="template" accept=".json,application/json" required
                       class="block w-full text-sm text-ink-700
                              file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0
                              file:text-sm file:font-semibold file:bg-brand-600 file:text-white
                              hover:file:bg-brand-700 file:cursor-pointer cursor-pointer
                              rounded-2xl border border-ink-200 bg-cream-2 p-2.5">
            </label>

            <label class="flex items-start gap-2.5 mt-4 cursor-pointer">
                <input type="checkbox" name="replace_demo" value="1" checked class="mt-0.5 rounded border-ink-300 text-brand-600 focus:ring-brand-400">
                <span class="text-sm text-ink-700">
                    <strong>Empezar limpio:</strong> borrar los cursos y artículos de blog de ejemplo del LMS, y dejar solo el contenido de esta plantilla.
                    <span class="block text-xs text-coral-600 mt-0.5"><i class="fa-solid fa-triangle-exclamation"></i> Desmárcala solo si quieres conservar lo que ya tienes.</span>
                </span>
            </label>

            <button type="submit"
                    class="mt-5 inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                <i class="fa-solid fa-upload text-xs"></i> Importar plantilla
            </button>
        </form>
    </div>

    {{-- Exportar el contenido actual como plantilla --}}
    <div class="mt-6 bg-white border border-ink-200/70 rounded-3xl shadow-soft p-7">
        <div class="flex items-center gap-3 mb-2">
            <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-file-export text-lg"></i>
            </span>
            <div>
                <h2 class="font-display font-extrabold text-lg text-ink-900">Exportar como plantilla</h2>
                <p class="text-sm text-ink-500">Convierte el contenido de tu sitio en un archivo <code class="px-1.5 py-0.5 rounded bg-cream-2 text-ink-700">.json</code> reutilizable o vendible.</p>
            </div>
        </div>

        <p class="mt-3 text-sm text-ink-600">
            Se exportarán <strong>{{ $exportCounts['courses'] ?? 0 }} curso(s)</strong> y <strong>{{ $exportCounts['lessons'] ?? 0 }} lección(es)</strong> activos (estructura, textos, quizzes y reseñas). Luego lo importas en otro Cursalia.
        </p>

        @if (($exportCounts['courses'] ?? 0) > 0)
            <a href="{{ route('admin.templates.export') }}"
               class="mt-5 inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                <i class="fa-solid fa-download text-xs"></i> Descargar plantilla (.json)
            </a>
        @else
            <p class="mt-4 text-sm text-ink-400"><i class="fa-solid fa-circle-info"></i> No hay cursos activos para exportar todavía.</p>
        @endif

        <p class="mt-3 text-xs text-ink-400"><i class="fa-solid fa-lightbulb text-brand-500 mr-1"></i> Consejo: para que la plantilla sirva en cualquier sitio, usa videos enlazados (YouTube/Bunny), no subidos al hosting.</p>
    </div>

    {{-- Cómo funciona --}}
    <div class="mt-6 bg-cream-2 border border-ink-200/70 rounded-3xl p-6 text-sm text-ink-600 leading-relaxed">
        <h3 class="font-bold text-ink-800 mb-2"><i class="fa-solid fa-circle-info text-brand-600 mr-1.5"></i> Cómo funciona</h3>
        <ol class="list-decimal pl-5 space-y-1.5">
            <li>Descarga la plantilla del nicho que quieras (un archivo <strong>.json</strong>).</li>
            <li>Súbela aquí y pulsa <strong>Importar</strong>: se crean la categoría, los cursos, sus módulos y lecciones.</li>
            <li>Ve a <a href="{{ route('admin.courses.index') }}" class="font-semibold text-brand-700 hover:text-brand-600">Cursos</a> y <strong>añade tus videos</strong> en cada lección.</li>
        </ol>
        <p class="mt-3 text-ink-500"><i class="fa-solid fa-shield-halved text-brand-500 mr-1"></i> Las plantillas solo traen estructura y textos; las miniaturas se generan aquí (sin código externo, por seguridad).</p>
    </div>

</div>
@endsection
