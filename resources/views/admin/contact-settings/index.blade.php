@extends('layouts.admin')

@section('title', 'Página de contacto')
@section('page-title', 'Página de contacto')
@section('page-subtitle', 'Textos, email receptor y mapa de la página /contact')

@section('content')

<form method="POST" action="{{ route('admin.contact-settings.update') }}" class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    @csrf

    <div class="space-y-6">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-heading text-brand-600"></i> Encabezado de la página
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título</label>
                <input type="text" name="title" value="{{ old('title', $setting->title) }}" maxlength="255" placeholder="Hablemos"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Subtítulo</label>
                <textarea name="subtitle" rows="2" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y">{{ old('subtitle', $setting->subtitle) }}</textarea>
                @error('subtitle')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-regular fa-paper-plane text-coral-500"></i> Formulario
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título del formulario</label>
                <input type="text" name="form_title" value="{{ old('form_title', $setting->form_title) }}" maxlength="255" placeholder="Envíanos un mensaje"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('form_title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Subtítulo del formulario</label>
                <input type="text" name="form_subtitle" value="{{ old('form_subtitle', $setting->form_subtitle) }}" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('form_subtitle')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Email que recibe los mensajes</label>
                <input type="email" name="receiver_email" value="{{ old('receiver_email', $setting->receiver_email) }}" maxlength="255" placeholder="hola@tusitio.com"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('receiver_email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-400 mt-1.5">Si lo dejas vacío, se usa el email del remitente configurado en el correo. Los mensajes también quedan guardados en <a href="{{ route('admin.messages.index') }}" class="text-brand-700 underline">Mensajes</a>.</p>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-sun-500"></i> Mapa
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">URL de inserción de Google Maps</label>
                <textarea name="map_embed_url" rows="3" maxlength="2000" placeholder="https://www.google.com/maps/embed?pb=…"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y">{{ old('map_embed_url', $setting->map_embed_url) }}</textarea>
                @error('map_embed_url')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-400 mt-1.5">En Google Maps: Compartir → Insertar un mapa → copia solo la URL del atributo <code>src</code>. Si lo dejas vacío, se muestra una tarjeta "Te respondemos online".</p>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-regular fa-clock text-brand-600"></i> Horario de atención
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Una línea por fila, con formato <code>Etiqueta | Valor</code></label>
                <textarea name="schedule" rows="4" maxlength="2000" placeholder="Lun – Vie | 9:00 – 18:00&#10;Sábado | 10:00 – 14:00&#10;Domingo | cerrado"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y">{{ old('schedule', $setting->schedule) }}</textarea>
                @error('schedule')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-400 mt-1.5">Si lo dejas vacío, se muestra un horario de ejemplo (Lun–Vie 9–18, etc.).</p>
            </div>
        </section>

        {{-- Gestión de tarjetas de contacto --}}
        <a href="{{ route('admin.contact-cards.index') }}" class="group flex items-center gap-4 bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 hover:border-brand-300 transition">
            <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600"><i class="fa-solid fa-address-card"></i></span>
            <div class="flex-1">
                <p class="font-display font-bold text-ink-900">Tarjetas de contacto</p>
                <p class="text-xs text-ink-500 mt-0.5">Los recuadros con email, teléfono, dirección… que se muestran arriba del formulario</p>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 group-hover:gap-2 transition-all">Gestionar <i class="fa-solid fa-arrow-right text-[10px]"></i></span>
        </a>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-3">
            <h3 class="font-display font-bold text-ink-900 text-sm">Vista previa</h3>
            <a href="{{ route('contact') }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-700 hover:text-brand-600">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Ver la página de contacto
            </a>
        </section>
        <div class="space-y-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
        </div>
    </aside>
</form>

@endsection
