@extends('layouts.admin')

@section('title', 'Menú de navegación')
@section('page-title', 'Menú de navegación')
@section('page-subtitle', 'Reordena, edita y activa los enlaces del header')

@section('content')

<div class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">

    {{-- ════════════════════ Lista de enlaces ════════════════════ --}}
    <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8"
             x-data="navigation()">

        <div class="flex items-start justify-between gap-3 mb-5">
            <div>
                <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                    <i class="fa-solid fa-bars text-brand-600"></i>
                    Enlaces del menú primario
                </h2>
                <p class="text-sm text-ink-500 mt-1">
                    <i class="fa-solid fa-arrows-up-down-left-right text-[10px] text-ink-400"></i>
                    Arrastra para reordenar · toggle para activar/desactivar
                </p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-50 border border-brand-200 text-xs font-semibold text-brand-700">
                {{ $links->count() }} enlaces
            </span>
        </div>

        {{-- Estado vacío --}}
        @if ($links->isEmpty())
            <div class="border-2 border-dashed border-ink-200 rounded-3xl p-10 text-center">
                <span class="grid place-items-center w-14 h-14 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
                    <i class="fa-solid fa-link-slash text-xl"></i>
                </span>
                <p class="font-display font-bold text-ink-900 mt-4">No hay enlaces todavía</p>
                <p class="text-sm text-ink-500 mt-1">Crea el primero desde el panel de la derecha →</p>
            </div>
        @else
            {{-- Lista ordenable --}}
            <ul id="nav-list" class="space-y-2">
                @foreach ($links as $link)
                    <li data-id="{{ $link->id }}"
                        x-data="navItem({{ Js::from([
                            'id'              => $link->id,
                            'title'           => $link->title,
                            'url'             => $link->url,
                            'open_in_new_tab' => (bool) $link->open_in_new_tab,
                            'is_active'       => (bool) $link->is_active,
                        ]) }})"
                        class="bg-cream-2 border border-ink-200/70 rounded-2xl transition"
                        :class="!data.is_active && 'opacity-50'">

                        {{-- Cabecera del item --}}
                        <div class="flex items-center gap-3 p-3">
                            <span class="cursor-grab active:cursor-grabbing grid place-items-center w-8 h-8 text-ink-400 hover:text-ink-700 transition handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </span>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-display font-bold text-ink-900 truncate" x-text="data.title"></span>
                                    <code class="text-[10px] px-2 py-0.5 rounded bg-white text-ink-500 border border-ink-200 truncate max-w-[200px]" x-text="data.url"></code>
                                    <template x-if="data.open_in_new_tab">
                                        <span class="text-[10px] font-bold uppercase text-coral-600 bg-coral-100 px-1.5 py-0.5 rounded">↗ Nueva pestaña</span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                {{-- Toggle on/off --}}
                                <button type="button" @click="toggle()"
                                        class="grid place-items-center w-10 h-10 rounded-xl transition"
                                        :class="data.is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-400'"
                                        :title="data.is_active ? 'Activo (click para ocultar)' : 'Oculto (click para mostrar)'">
                                    <i class="fa-solid" :class="data.is_active ? 'fa-eye' : 'fa-eye-slash'"></i>
                                </button>
                                {{-- Editar --}}
                                <button type="button" @click="editing = !editing"
                                        class="grid place-items-center w-10 h-10 rounded-xl bg-white border border-ink-200 text-ink-700 hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200 transition">
                                    <i class="fa-solid" :class="editing ? 'fa-xmark' : 'fa-pen'"></i>
                                </button>
                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('admin.navigation.destroy', $link) }}"
                                      onsubmit="return confirm('¿Eliminar el enlace «{{ addslashes($link->title) }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="grid place-items-center w-10 h-10 rounded-xl bg-white border border-ink-200 text-ink-700 hover:bg-coral-50 hover:text-coral-600 hover:border-coral-200 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Form de edición (Alpine) --}}
                        <div x-show="editing" x-collapse>
                            <form method="POST" action="{{ route('admin.navigation.update', $link) }}"
                                  class="border-t border-ink-200/70 p-4 grid sm:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" :value="data.is_active ? 1 : 0">

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1.5">Título</label>
                                    <input type="text" name="title" x-model="data.title" required maxlength="60"
                                        class="w-full px-3 py-2 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1.5">URL</label>
                                    <input type="text" name="url" x-model="data.url" required maxlength="255"
                                        class="w-full px-3 py-2 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-sm font-mono">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="inline-flex items-center gap-2 text-xs text-ink-700">
                                        <input type="checkbox" name="open_in_new_tab" value="1"
                                               :checked="data.open_in_new_tab" @change="data.open_in_new_tab = $event.target.checked"
                                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                                        Nueva pestaña
                                    </label>
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition">
                                        <i class="fa-solid fa-check text-xs"></i> Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <p class="mt-5 text-xs text-ink-400 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-bolt text-brand-500"></i>
            Los cambios son visibles al instante en el sitio (el caché se invalida solo).
        </p>
    </section>

    {{-- ════════════════════ Añadir nuevo ════════════════════ --}}
    <aside class="lg:sticky lg:top-24">
        <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
            <h3 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-1">
                <i class="fa-solid fa-plus text-coral-500"></i>
                Añadir enlace
            </h3>
            <p class="text-sm text-ink-500 mb-4">El nuevo enlace aparecerá al final del menú.</p>

            <form method="POST" action="{{ route('admin.navigation.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="title">Título</label>
                    <input id="title" type="text" name="title" required maxlength="60" placeholder="Ej. Plantillas"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="url">URL</label>
                    <input id="url" type="text" name="url" required maxlength="255" placeholder="/templates  o  https://…"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white text-sm font-mono">
                    <p class="text-xs text-ink-400 mt-1.5">Puedes usar rutas internas (<code>/cursos</code>) o URLs externas (<code>https://…</code>).</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-700 cursor-pointer">
                    <input type="checkbox" name="open_in_new_tab" value="1"
                           class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                    Abrir en nueva pestaña
                </label>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-soft transition">
                    <i class="fa-solid fa-plus text-xs"></i> Añadir al menú
                </button>
            </form>
        </div>

        {{-- Ayuda --}}
        <div class="mt-5 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-5 shadow-soft">
            <i class="fa-solid fa-lightbulb text-sun-300 text-lg"></i>
            <h4 class="font-display font-bold mt-2">Truco</h4>
            <p class="text-sm text-brand-50/90 mt-1.5">
                El enlace llamado <b>"Categorías"</b> se convierte automáticamente en un <b>dropdown</b> con las categorías reales de tus cursos.
            </p>
        </div>
    </aside>
</div>

<script>
// Sortable.js está en el bundle Vite (window.Sortable). Si no, lo cargamos del CDN.
function ensureSortable(cb) {
    if (typeof window.Sortable === 'function') return cb();
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js';
    s.onload = cb;
    document.head.appendChild(s);
}

function navigation() {
    return {
        init() {
            ensureSortable(() => {
                const list = document.getElementById('nav-list');
                if (! list) return;
                window.Sortable.create(list, {
                    handle: '.handle',
                    animation: 180,
                    ghostClass: 'opacity-30',
                    onEnd: () => this.persistOrder(),
                });
            });
        },
        async persistOrder() {
            const ids = [...document.querySelectorAll('#nav-list > li')].map((l) => l.dataset.id);
            try {
                const res = await fetch('{{ route('admin.navigation.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order: ids }),
                });
                if (! res.ok) throw new Error('reorder failed');
            } catch (e) {
                alert('No se pudo guardar el orden. Recarga e inténtalo de nuevo.');
            }
        },
    };
}

function navItem(data) {
    return {
        data,
        editing: false,
        async toggle() {
            try {
                const res = await fetch(`/admin/navigation/${this.data.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const json = await res.json();
                if (json.ok) {
                    this.data.is_active = json.is_active;
                }
            } catch (e) { /* silent */ }
        },
    };
}
</script>

@endsection
