@extends('layouts.admin')

@section('title', 'Mensajes')
@section('page-title', 'Mensajes de contacto')
@section('page-subtitle', 'Lo que te escriben desde el formulario de contacto')

@section('content')

<div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-cream-2 text-ink-500 text-left">
            <tr>
                <th class="px-4 py-3 font-semibold">De</th>
                <th class="px-4 py-3 font-semibold hidden md:table-cell">Asunto</th>
                <th class="px-4 py-3 font-semibold hidden lg:table-cell">Fecha</th>
                <th class="px-4 py-3 font-semibold text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-100">
            @forelse ($messages as $m)
                <tr class="hover:bg-cream-2/50 {{ $m->read_at ? '' : 'bg-brand-50/40' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.messages.show', $m) }}" class="block">
                            <span class="font-semibold text-ink-800">{{ $m->name }}</span>
                            @unless($m->read_at)<span class="ml-2 text-[10px] font-bold bg-brand-600 text-white rounded-full px-1.5 py-0.5">NUEVO</span>@endunless
                            <span class="block text-xs text-ink-400">{{ $m->email }}</span>
                        </a>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-ink-600">{{ \Illuminate\Support\Str::limit($m->subject ?: '(sin asunto)', 40) }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell text-ink-400 text-xs">{{ $m->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.messages.show', $m) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Ver"><i class="fa-solid fa-eye text-xs"></i></a>
                            <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" onsubmit="return confirm('¿Eliminar este mensaje?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-ink-400">Aún no hay mensajes. Aparecerán aquí cuando alguien use el formulario de contacto.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $messages->links() }}</div>

@endsection
