@extends('layouts.admin')

@section('title', 'Páginas')
@section('page-title', 'Páginas personalizadas y legales')
@section('page-subtitle', 'Privacidad, términos, y cualquier página de texto que necesites')

@section('content')

<div class="flex items-center justify-end mb-6">
    <a href="{{ route('admin.custom-pages.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva página
    </a>
</div>

@if ($items->isNotEmpty())
    <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-cream-2 text-ink-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Título</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell">URL</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                    <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($items as $item)
                    <tr class="hover:bg-cream-2/50">
                        <td class="px-4 py-3 font-semibold text-ink-800">{{ $item->title }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-ink-500 font-mono text-xs">/{{ $item->slug }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $item->status ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                                {{ $item->status ? 'Publicada' : 'Borrador' }}
                            </span>
                            @if ($item->show_at_nav)<span class="ml-1 inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-sun-100 text-sun-600">En menú</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ url('/'.$item->slug) }}" target="_blank" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Ver"><i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>
                                <a href="{{ route('admin.custom-pages.edit', $item) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Editar"><i class="fa-solid fa-pen text-xs"></i></a>
                                <form method="POST" action="{{ route('admin.custom-pages.destroy', $item) }}" onsubmit="return confirm('¿Eliminar esta página?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $items->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-regular fa-file-lines text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay páginas</p>
        <p class="text-sm text-ink-500 mt-1">Crea tu política de privacidad, términos u otras páginas con el botón de arriba.</p>
    </div>
@endif

@endsection
