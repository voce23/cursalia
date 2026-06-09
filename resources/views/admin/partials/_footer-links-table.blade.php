@if ($items->isNotEmpty())
    <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-cream-2 text-ink-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Texto</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell">URL</th>
                    <th class="px-4 py-3 font-semibold hidden sm:table-cell">Orden</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                    <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($items as $item)
                    <tr class="hover:bg-cream-2/50">
                        <td class="px-4 py-3 font-semibold text-ink-800">{{ $item->title }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-ink-500 font-mono text-xs">{{ \Illuminate\Support\Str::limit($item->url, 40) }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell text-ink-400">{{ $item->sort_order }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $item->is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                                {{ $item->is_active ? 'Visible' : 'Oculto' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route($editRoute, $item) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Editar"><i class="fa-solid fa-pen text-xs"></i></a>
                                <form method="POST" action="{{ route($destroyRoute, $item) }}" onsubmit="return confirm('¿Eliminar este enlace?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
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
        <i class="fa-solid fa-link text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay enlaces</p>
        <p class="text-sm text-ink-500 mt-1">Crea el primero con el botón de arriba.</p>
    </div>
@endif
