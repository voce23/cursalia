@extends('layouts.admin')

@section('title', 'Migrador PRO')
@section('page-title', 'Migrador PRO')
@section('page-subtitle', 'Muda o clona tu academia a otro hosting o dominio, en pocos clics')

@section('content')

@if (! $proActive)
    {{-- ───── NO ACTIVO: pedir la llave PRO ───── --}}
    <div class="max-w-2xl">
        <div class="rounded-3xl border border-ink-200 bg-white p-8 text-center shadow-soft">
            <div class="w-16 h-16 rounded-full grid place-items-center mx-auto text-white text-3xl mb-4" style="background:linear-gradient(135deg,#10B981,#047857)">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <h2 class="font-display font-extrabold text-2xl text-ink-900">Activa Cursalia PRO</h2>
            <p class="text-ink-600 mt-2">El <strong>Migrador</strong> empaqueta TODO tu sitio (archivos + base de datos) y te da un <strong>instalador web</strong> para revivirlo en otro hosting o clonarlo a otro dominio. Tu llave PRO también desbloquea los demás complementos PRO.</p>

            <form method="POST" action="{{ route('admin.migrador.activate') }}" class="mt-6 max-w-md mx-auto">
                @csrf
                <label class="block text-sm font-semibold text-ink-700 mb-1 text-left">Llave PRO</label>
                <input type="text" name="pro_key" value="{{ old('pro_key') }}" placeholder="PRO-XXXXXXXXXXXX"
                       class="w-full px-4 py-3 rounded-xl border border-ink-200 font-mono text-center focus:outline-none focus:ring-2 focus:ring-brand-300 focus:border-brand-400 transition">
                @error('pro_key')<p class="text-xs text-coral-600 mt-1 text-left">{{ $message }}</p>@enderror
                <button type="submit" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-extrabold text-white" style="background:linear-gradient(135deg,#10B981,#047857)">
                    <i class="fa-solid fa-key"></i> Activar PRO
                </button>
                <p class="mt-3 text-xs text-ink-400">¿No tienes tu llave? <a href="https://cursalia.org/plugins" target="_blank" rel="noopener" class="text-brand-600 font-semibold hover:underline">Consigue Cursalia PRO</a>.</p>
            </form>
        </div>
    </div>
@else
    {{-- ───── ACTIVO: generar paquete + instalador ───── --}}
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
        <p class="text-sm text-green-800 font-semibold">Cursalia PRO activado. Ya puedes crear paquetes de migración.</p>
    </div>

    <div class="max-w-3xl space-y-6">

        {{-- Generar --}}
        <div class="rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
            <h3 class="font-display font-bold text-lg text-ink-900"><i class="fa-solid fa-box text-brand-500"></i> Crear paquete de migración</h3>
            <p class="text-sm text-ink-500 mt-1">Genera UN ZIP con todo tu sitio (incluidas las dependencias) listo para montarse en un hosting vacío. Se prepara al momento.</p>

            <form method="POST" action="{{ route('admin.migrador.build') }}" class="mt-4"
                  onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Generando… (puede tardar)';">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                    <i class="fa-solid fa-box"></i> Crear paquete de migración
                </button>
            </form>
            <p class="mt-2 text-xs text-ink-400">El paquete incluye <code>vendor</code>, por lo que pesa bastante (≈100 MB). Es normal.</p>
        </div>

        {{-- Lista de paquetes --}}
        @if (!empty($packages))
            <div class="rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
                <h3 class="font-display font-bold text-lg text-ink-900 mb-3"><i class="fa-solid fa-box-archive text-ink-400"></i> Paquete listo</h3>
                <ul class="divide-y divide-ink-100">
                    @foreach ($packages as $p)
                        <li class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-ink-800 truncate">
                                    <i class="fa-solid fa-file-zipper text-brand-500"></i>
                                    {{ \Illuminate\Support\Carbon::createFromTimestamp($p['mtime'])->translatedFormat('d M Y · H:i') }}
                                </div>
                                <div class="text-xs text-ink-400 truncate">{{ $p['name'] }} · {{ number_format($p['size'] / 1048576, 1) }} MB</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('admin.migrador.download', $p['name']) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100 font-semibold text-sm px-3 py-2 transition">
                                    <i class="fa-solid fa-download"></i> Paquete
                                </a>
                                <a href="{{ route('admin.migrador.installer') }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-ink-100 text-ink-700 hover:bg-ink-200 font-semibold text-sm px-3 py-2 transition">
                                    <i class="fa-solid fa-gear"></i> Instalador
                                </a>
                                <form method="POST" action="{{ route('admin.migrador.destroy', $p['name']) }}"
                                      onsubmit="return confirm('¿Eliminar este paquete del servidor?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-xl text-ink-400 hover:bg-coral-50 hover:text-coral-700 text-sm px-3 py-2 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5 rounded-2xl bg-cream-2 border border-ink-200/60 px-4 py-3 text-sm text-ink-600">
                    <p class="font-semibold text-ink-800 mb-1"><i class="fa-solid fa-list-check text-brand-500"></i> Cómo usarlo en el hosting nuevo</p>
                    <ol class="list-decimal ml-5 space-y-0.5">
                        <li>Descarga <strong>Paquete</strong> e <strong>Instalador</strong>.</li>
                        <li>Súbelos a la carpeta pública del nuevo dominio.</li>
                        <li>Abre <code>https://tudominio/instalador.php</code> y sigue el asistente (4 pasos).</li>
                        <li>Al terminar, <strong>borra</strong> el instalador y el paquete.</li>
                    </ol>
                </div>
            </div>
        @endif

    </div>
@endif

@endsection
