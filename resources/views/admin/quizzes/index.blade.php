@extends('layouts.admin')

@section('title', 'Autoevaluaciones')
@section('page-title', 'Autoevaluaciones')
@section('page-subtitle', 'Adjunta un quiz de repaso a cada lección (FREE · sin certificado)')

@section('content')

@if (session('success'))
    <div class="mb-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[220px]">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-ink-400 text-sm"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar lección…"
               class="w-full pl-11 pr-4 py-2.5 rounded-2xl bg-white border border-ink-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 text-sm">
    </div>
    <button class="px-5 py-2.5 rounded-2xl bg-ink-900 text-white text-sm font-semibold hover:bg-ink-800 transition">Buscar</button>
</form>

<div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-cream-2 text-ink-500 text-xs uppercase tracking-wider">
            <tr>
                <th class="text-left font-semibold px-5 py-3">Lección</th>
                <th class="text-left font-semibold px-5 py-3 hidden md:table-cell">Curso</th>
                <th class="text-left font-semibold px-5 py-3">Autoevaluación</th>
                <th class="text-right font-semibold px-5 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-100">
            @forelse ($lessons as $lesson)
                @php $quiz = $quizzes->get($lesson->id); @endphp
                <tr class="hover:bg-cream-2/40 transition">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-ink-900 line-clamp-1">{{ $lesson->title }}</p>
                        @if ($lesson->chapter)
                            <p class="text-xs text-ink-400">{{ $lesson->chapter->title }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-ink-600">
                        {{ optional($lesson->course)->title ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        @if ($quiz)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold">
                                <i class="fa-solid fa-circle-check"></i> Activa
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-cream-2 text-ink-400 text-xs">
                                Sin quiz
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        @if ($quiz)
                            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink-900 text-white text-xs font-semibold hover:bg-ink-800 transition">
                                <i class="fa-solid fa-pen"></i> Editar
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar esta autoevaluación?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-coral-100 text-coral-700 text-xs font-semibold hover:bg-coral-200 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.quizzes.create', ['lesson' => $lesson->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700 transition">
                                <i class="fa-solid fa-plus"></i> Crear quiz
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-ink-400">
                        No hay lecciones todavía.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $lessons->links() }}</div>

@endsection
