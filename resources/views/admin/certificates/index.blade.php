@extends('layouts.admin')

@section('title', 'Certificados PRO')
@section('page-title', 'Certificados PRO')
@section('page-subtitle', 'Tus alumnos descargan un certificado al completar el curso')

@section('content')

@if (! $proActive)
    @include('admin.partials._pro-lock', [
        'titulo' => 'Certificados (PRO)',
        'desc' => 'Entrega certificados de finalización a tus alumnos cuando completan un curso. Actívalo con tu llave Cursalia PRO.',
    ])
@else
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
        <p class="text-sm text-green-800 font-semibold">Cursalia PRO activado · Certificados habilitados.</p>
    </div>

    <div class="max-w-2xl rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
        <h3 class="font-display font-bold text-lg text-ink-900"><i class="fa-solid fa-award text-brand-500"></i> Cómo funcionan</h3>
        <p class="text-sm text-ink-500 mt-1 leading-relaxed">
            Cuando un alumno completa el <strong>100% de las lecciones</strong> de un curso, le aparece el botón
            <strong>«Descargar mi certificado»</strong> en el reproductor. El certificado lleva su nombre, el curso,
            la fecha, tu academia y un código de verificación, y puede <strong>guardarlo como PDF</strong> o imprimirlo.
        </p>

        <div class="mt-4 rounded-2xl bg-cream-2 border border-ink-200/60 px-4 py-3 text-sm text-ink-600">
            <p class="font-semibold text-ink-800 mb-1"><i class="fa-solid fa-list-check text-brand-500"></i> Para que un curso emita certificado</p>
            <ol class="list-decimal ml-5 space-y-0.5">
                <li>Ve a <a href="{{ route('admin.courses.index') }}" class="text-brand-700 font-semibold hover:underline">Cursos</a> y edita el curso.</li>
                <li>Activa la opción de <strong>certificado</strong> en ese curso.</li>
                <li>Listo: tus alumnos lo reciben al terminarlo.</li>
            </ol>
        </div>

        <p class="mt-3 text-xs text-ink-400"><i class="fa-solid fa-shield-halved text-brand-500 mr-1"></i> No requiere instalar nada: el certificado se genera al momento, sin librerías externas.</p>
    </div>
@endif

@endsection
