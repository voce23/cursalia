@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', 'Ficha de usuario')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.users.index') }}" class="hover:text-brand-700">Usuarios</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $user->name }}</span>
</nav>

<div class="grid lg:grid-cols-[360px_1fr] gap-6 items-start">

    {{-- Tarjeta de perfil --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
        <div class="flex items-center gap-4">
            @if ($user->image)
                <img src="{{ asset('storage/'.$user->image) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover">
            @else
                <span class="grid place-items-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            @endif
            <div class="min-w-0">
                <h2 class="font-display font-extrabold text-lg text-ink-900 truncate">{{ $user->name }}</h2>
                <p class="text-sm text-ink-500 truncate">{{ $user->email }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4">
            @if ($user->role === 'instructor')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-sun-100 text-ink-800 rounded-full px-2.5 py-1"><i class="fa-solid fa-chalkboard-user"></i> Instructor</span>
            @else
                <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-ink-100 text-ink-500 rounded-full px-2.5 py-1"><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
            @endif
            @if ($user->is_active)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-brand-50 text-brand-700 rounded-full px-2.5 py-1"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Activo</span>
            @else
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-coral-50 text-coral-600 rounded-full px-2.5 py-1"><span class="w-1.5 h-1.5 rounded-full bg-coral-400"></span> Inactivo</span>
            @endif
        </div>

        <dl class="mt-5 space-y-2.5 text-sm">
            @if ($user->headline)
                <div class="flex gap-2"><dt class="text-ink-400 w-24 shrink-0">Titular</dt><dd class="text-ink-700">{{ $user->headline }}</dd></div>
            @endif
            @if ($user->phone)
                <div class="flex gap-2"><dt class="text-ink-400 w-24 shrink-0">Teléfono</dt><dd class="text-ink-700">{{ $user->phone }}</dd></div>
            @endif
            <div class="flex gap-2"><dt class="text-ink-400 w-24 shrink-0">Registro</dt><dd class="text-ink-700">{{ $user->created_at?->format('d/m/Y') }}</dd></div>
        </dl>

        <div class="mt-5 pt-5 border-t border-ink-100 flex flex-wrap gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-semibold text-sm bg-brand-600 text-white hover:bg-brand-700 transition">
                <i class="fa-solid fa-pen text-xs"></i> Editar
            </a>
            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-semibold text-sm transition {{ $user->is_active ? 'bg-coral-50 text-coral-600 hover:bg-coral-100' : 'bg-cream-2 text-ink-700 hover:bg-ink-100' }}">
                    <i class="fa-solid {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i> {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
            @if ($user->role === 'instructor' && $user->document)
                <a href="{{ route('admin.instructor-requests.download', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-semibold text-sm bg-cream-2 text-ink-700 hover:bg-ink-100 transition">
                    <i class="fa-solid fa-file-arrow-down text-xs"></i> Documento
                </a>
            @endif
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar a «{{ $user->name }}»? Sus inscripciones y datos se conservarán pero ya no podrá acceder.')" class="ml-auto">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-semibold text-sm bg-coral-50 text-coral-600 hover:bg-coral-100 transition">
                    <i class="fa-solid fa-trash text-xs"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    {{-- Estadísticas + actividad --}}
    <div class="space-y-6">
        <div class="grid grid-cols-3 gap-4">
            @foreach ([['Cursos', $user->courses_count, 'fa-book-open'], ['Inscripciones', $user->enrollments_count, 'fa-graduation-cap'], ['Pedidos', $user->orders_count, 'fa-receipt']] as [$label, $count, $icon])
                <div class="bg-white border border-ink-200/70 rounded-2xl shadow-soft p-5 text-center">
                    <i class="fa-solid {{ $icon }} text-brand-500"></i>
                    <p class="font-display font-extrabold text-2xl text-ink-900 mt-2">{{ $count }}</p>
                    <p class="text-xs text-ink-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        @if ($user->role === 'instructor' && $user->courses->isNotEmpty())
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
                <h3 class="font-display font-bold text-ink-900 mb-3">Cursos que imparte</h3>
                <ul class="divide-y divide-ink-100 text-sm">
                    @foreach ($user->courses as $c)
                        <li class="py-2.5 text-ink-700">{{ $c->title ?? $c->name ?? 'Curso #'.$c->id }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
            <h3 class="font-display font-bold text-ink-900 mb-3">Inscripciones recientes</h3>
            @if ($user->enrollments->isNotEmpty())
                <ul class="divide-y divide-ink-100 text-sm">
                    @foreach ($user->enrollments as $e)
                        <li class="py-2.5 flex items-center justify-between gap-3">
                            <span class="text-ink-700">{{ $e->course?->title ?? $e->course?->name ?? 'Curso #'.$e->course_id }}</span>
                            <span class="text-xs text-ink-400">{{ $e->created_at?->format('d/m/Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-ink-400">Sin inscripciones todavía.</p>
            @endif
        </div>
    </div>
</div>

@endsection
