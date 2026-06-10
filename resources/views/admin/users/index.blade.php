@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Estudiantes e instructores registrados')

@section('content')

<div class="flex items-center justify-end mb-4">
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition text-sm">
        <i class="fa-solid fa-user-plus text-xs"></i> Nuevo usuario
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
    <div class="relative flex-1 min-w-[220px]">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-ink-400 text-sm"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email…"
            class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
    </div>
    <select name="role" class="px-4 py-2.5 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
        <option value="">Todos los roles</option>
        <option value="student" @selected(request('role') === 'student')>Estudiantes</option>
        <option value="instructor" @selected(request('role') === 'instructor')>Instructores</option>
    </select>
    <select name="status" class="px-4 py-2.5 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
        <option value="">Activos e inactivos</option>
        <option value="active" @selected(request('status') === 'active')>Solo activos</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Solo inactivos</option>
    </select>
    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-semibold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition text-sm">
        <i class="fa-solid fa-filter text-xs"></i> Filtrar
    </button>
    @if (request()->hasAny(['search', 'role', 'status']))
        <a href="{{ route('admin.users.index') }}" class="text-sm text-ink-500 hover:text-brand-700">Limpiar</a>
    @endif
</form>

@if ($users->isNotEmpty())
    <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-cream-2 text-ink-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Usuario</th>
                    <th class="px-4 py-3 font-semibold">Rol</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell text-center">Cursos</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell text-center">Inscrip.</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                    <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($users as $u)
                    <tr class="hover:bg-cream-2/50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $u) }}" class="font-medium text-ink-800 hover:text-brand-700">{{ $u->name }}</a>
                            <span class="block text-xs text-ink-400">{{ $u->email }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($u->role === 'instructor')
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-sun-100 text-ink-800 rounded-full px-2.5 py-1"><i class="fa-solid fa-chalkboard-user"></i> Instructor</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-ink-100 text-ink-500 rounded-full px-2.5 py-1"><i class="fa-solid fa-user-graduate"></i> Estudiante</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-center text-ink-600">{{ $u->courses_count }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-center text-ink-600">{{ $u->enrollments_count }}</td>
                        <td class="px-4 py-3">
                            @if ($u->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-brand-50 text-brand-700 rounded-full px-2.5 py-1"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Activo</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-coral-50 text-coral-600 rounded-full px-2.5 py-1"><span class="w-1.5 h-1.5 rounded-full bg-coral-400"></span> Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.users.show', $u) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-brand-50 hover:text-brand-700" title="Ver ficha"><i class="fa-solid fa-eye text-xs"></i></a>
                                <a href="{{ route('admin.users.edit', $u) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-brand-50 hover:text-brand-700" title="Editar / cambiar contraseña"><i class="fa-solid fa-pen text-xs"></i></a>
                                <form method="POST" action="{{ route('admin.users.toggle', $u) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="grid place-items-center w-8 h-8 rounded-lg {{ $u->is_active ? 'text-coral-500 hover:bg-coral-50' : 'text-brand-600 hover:bg-brand-50' }}" title="{{ $u->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fa-solid {{ $u->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('¿Eliminar a «{{ $u->name }}»? Sus inscripciones y datos se conservarán pero ya no podrá acceder.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-users text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">No hay usuarios que coincidan</p>
        <p class="text-sm text-ink-500 mt-1">Ajusta los filtros o espera a que se registren estudiantes.</p>
    </div>
@endif

@endsection
