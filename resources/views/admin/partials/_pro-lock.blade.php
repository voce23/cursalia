{{-- Candado PRO reutilizable: muestra el formulario de activación de la llave.
     Parámetros opcionales: $titulo, $desc --}}
<div class="max-w-2xl">
    <div class="rounded-3xl border border-ink-200 bg-white p-8 text-center shadow-soft">
        <div class="w-16 h-16 rounded-full grid place-items-center mx-auto text-white text-3xl mb-4" style="background:linear-gradient(135deg,#10B981,#047857)">
            <i class="fa-solid fa-crown"></i>
        </div>
        <h2 class="font-display font-extrabold text-2xl text-ink-900">{{ $titulo ?? 'Función PRO' }}</h2>
        <p class="text-ink-600 mt-2">{{ $desc ?? 'Activa Cursalia PRO con tu llave para desbloquear esta función y todos los complementos PRO.' }}</p>

        <form method="POST" action="{{ route('admin.pro.activate') }}" class="mt-6 max-w-md mx-auto">
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
