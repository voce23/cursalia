@extends('layouts.admin')

@section('title', 'Administradores')
@section('page-title', 'Administradores')
@section('page-subtitle', 'Superadmins con acceso completo al panel')

@section('content')

<div class="flex items-center justify-between gap-4 mb-5">
    <p class="text-sm text-ink-500">{{ $admins->total() }} administrador(es)</p>
    <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition text-sm">
        <i class="fa-solid fa-user-plus text-xs"></i> Nuevo administrador
    </a>
</div>

<div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-cream-2 text-ink-500 text-left">
            <tr>
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold hidden sm:table-cell">Email</th>
                <th class="px-4 py-3 font-semibold hidden md:table-cell">Alta</th>
                <th class="px-4 py-3 font-semibold text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-100">
            @foreach ($admins as $a)
                <tr class="hover:bg-cream-2/50">
                    <td class="px-4 py-3">
                        <span class="font-medium text-ink-800">{{ $a->name }}</span>
                        @if ($a->id === auth('admin')->id())
                            <span class="ml-1 text-[11px] font-bold bg-brand-100 text-brand-700 rounded-full px-2 py-0.5">tú</span>
                        @endif
                        <span class="block sm:hidden text-xs text-ink-400">{{ $a->email }}</span>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell text-ink-600">{{ $a->email }}</td>
                    <td class="px-4 py-3 hidden md:table-cell text-ink-400 text-xs">{{ $a->created_at?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.admins.edit', $a) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-brand-50 hover:text-brand-700" title="Editar / cambiar contraseña"><i class="fa-solid fa-pen text-xs"></i></a>
                            @if ($a->id !== auth('admin')->id())
                                <form method="POST" action="{{ route('admin.admins.destroy', $a) }}" onsubmit="return confirm('¿Eliminar a «{{ $a->name }}»? Perderá el acceso al panel.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button>
                                </form>
                            @else
                                <span class="grid place-items-center w-8 h-8 text-ink-200" title="No puedes eliminar tu propia cuenta"><i class="fa-solid fa-trash text-xs"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $admins->links() }}</div>

@endsection
