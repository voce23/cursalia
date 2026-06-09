@extends('layouts.dashboard')

@section('title', 'Hazte instructor')
@section('page-title', 'Hazte instructor')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Cabecera --}}
    <div class="bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-3xl p-7 sm:p-9 shadow-lift relative overflow-hidden">
        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold">
                <i class="fa-solid fa-chalkboard-user"></i> Conviértete en instructor
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-4">Comparte lo que sabes</h2>
            <p class="text-brand-50/90 mt-3 max-w-lg text-sm sm:text-base">
                Crea tus propios cursos y enseña a una comunidad de estudiantes. Envía tu solicitud y, una vez aprobada, tendrás acceso al panel de instructor.
            </p>
        </div>
    </div>

    {{-- Pasos --}}
    <div class="grid sm:grid-cols-3 gap-4 mt-8">
        @foreach ([
            ['fa-file-arrow-up', '1. Envía tu solicitud', 'Sube un documento que acredite tu experiencia (CV, certificado o identificación).'],
            ['fa-user-check', '2. Revisamos', 'Un administrador revisa tu solicitud. Te avisaremos por email.'],
            ['fa-chalkboard', '3. Empieza a enseñar', 'Al aprobarte, accedes al panel para crear y publicar tus cursos.'],
        ] as $step)
            <div class="bg-white rounded-2xl border border-ink-200/70 p-5 shadow-soft">
                <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600 text-lg"><i class="fa-solid {{ $step[0] }}"></i></span>
                <h3 class="font-display font-bold text-ink-900 mt-4 text-sm">{{ $step[1] }}</h3>
                <p class="text-xs text-ink-500 mt-1.5 leading-relaxed">{{ $step[2] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-3xl border border-ink-200/70 p-7 sm:p-8 shadow-soft mt-8">
        <h3 class="font-display font-extrabold text-xl text-ink-900">Tu solicitud</h3>
        <p class="text-sm text-ink-500 mt-1">Sube un documento de respaldo para completar tu solicitud.</p>

        @if ($errors->any())
            <div class="mt-5 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('student.become-instructor.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Documento de respaldo <span class="text-coral-500">*</span></label>
                <input type="file" name="document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       class="block w-full text-sm text-ink-600 file:mr-4 file:rounded-full file:border-0 file:bg-brand-50 file:px-5 file:py-2.5 file:text-brand-700 file:font-semibold hover:file:bg-brand-100 cursor-pointer">
                <p class="mt-1.5 text-xs text-ink-400">PDF, Word, JPG o PNG · máximo 12 MB.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-soft hover:bg-brand-700 transition">
                    <i class="fa-solid fa-paper-plane"></i> Enviar solicitud
                </button>
                <a href="{{ route('student.dashboard') }}" class="px-5 py-3 rounded-2xl text-ink-600 font-semibold hover:bg-ink-100 transition">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
