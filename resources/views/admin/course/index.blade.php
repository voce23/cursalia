@extends('layouts.admin')

@section('title', 'Cursos')
@section('page-title', 'Cursos')
@section('page-subtitle', 'Crea y administra los cursos de tu academia')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form method="GET" class="flex items-center gap-2">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-xs"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar curso o instructor…"
                   class="rounded-full border border-ink-200 pl-9 pr-4 py-2 text-sm w-64 max-w-full focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
        </div>
        <select name="approval" onchange="this.form.submit()" class="rounded-full border border-ink-200 px-3 py-2 text-sm">
            <option value="">Todos</option>
            <option value="approved" @selected(request('approval')==='approved')>Aprobados</option>
            <option value="pending" @selected(request('approval')==='pending')>Pendientes</option>
            <option value="rejected" @selected(request('approval')==='rejected')>Rechazados</option>
        </select>
    </form>
    <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition">
        <i class="fa-solid fa-plus"></i> Nuevo curso
    </a>
</div>

<div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-cream-2 text-ink-500 text-left">
            <tr>
                <th class="px-4 py-3 font-semibold">Curso</th>
                <th class="px-4 py-3 font-semibold hidden md:table-cell">Instructor</th>
                <th class="px-4 py-3 font-semibold hidden lg:table-cell">Contenido</th>
                <th class="px-4 py-3 font-semibold">Estado</th>
                <th class="px-4 py-3 font-semibold text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-100">
            @forelse ($courses as $course)
                <tr class="hover:bg-cream-2/50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-9 rounded-lg bg-cream-2 overflow-hidden shrink-0 grid place-items-center">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-image text-ink-300"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-ink-800 truncate max-w-xs">{{ $course->title }}</p>
                                <p class="text-xs text-ink-400">{{ $course->category?->name ?? '—' }} · {{ $course->price > 0 ? number_format($course->price,2).' €' : 'Gratis' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-ink-600">{{ $course->instructor?->name ?? '—' }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell text-ink-500 text-xs">{{ $course->chapters_count ?? 0 }} cap · {{ $course->lessons_count ?? 0 }} lecc.</td>
                    <td class="px-4 py-3">
                        @if ($course->is_approved === 'approved')
                            <span class="text-xs bg-brand-50 text-brand-700 rounded-full px-2.5 py-1 font-semibold">Publicado</span>
                        @elseif ($course->is_approved === 'pending')
                            <span class="text-xs bg-sun-100 text-sun-700 rounded-full px-2.5 py-1 font-semibold">Pendiente</span>
                        @else
                            <span class="text-xs bg-coral-50 text-coral-600 rounded-full px-2.5 py-1 font-semibold">Rechazado</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.courses.content', $course) }}" class="inline-flex items-center gap-1 h-8 px-3 rounded-lg bg-brand-50 text-brand-700 text-xs font-bold hover:bg-brand-100" title="Capítulos y lecciones"><i class="fa-solid fa-list-ol"></i> Contenido</a>
                            <a href="{{ route('admin.courses.edit', $course) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Editar"><i class="fa-solid fa-pen text-xs"></i></a>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('¿Eliminar este curso y todo su contenido?')">
                                @csrf @method('DELETE')
                                <button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-ink-400">
                    Aún no hay cursos. <a href="{{ route('admin.courses.create') }}" class="text-brand-600 font-semibold">Crea el primero</a>.
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $courses->links() }}</div>

@endsection
