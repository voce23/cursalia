@extends('layouts.admin')

@section('title', 'Pie de página')
@section('page-title', 'Pie de página')
@section('page-subtitle', 'Lo que aparece en el footer de todas las páginas')

@section('content')

<form method="POST" action="{{ route('admin.footer.update') }}" class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    @csrf

    <div class="space-y-6">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-align-left text-brand-600"></i> Texto e información
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Descripción (texto bajo el logo)</label>
                <textarea name="description" rows="3" maxlength="1200"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y">{{ old('description', $setting->description) }}</textarea>
                @error('description')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Texto inferior (copyright extra)</label>
                <input type="text" name="bottom_text" value="{{ old('bottom_text', $setting->bottom_text) }}" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('bottom_text')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-address-book text-coral-500"></i> Columna de contacto
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título de la columna</label>
                <input type="text" name="contact_title" value="{{ old('contact_title', $setting->contact_title) }}" required maxlength="120"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('contact_title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" maxlength="120"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" maxlength="60"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    @error('phone')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Dirección</label>
                <input type="text" name="address" value="{{ old('address', $setting->address) }}" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('address')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </section>

        {{-- Gestión de enlaces y redes (CRUD aparte) --}}
        <section class="grid sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.footer-column-one.index') }}" class="group bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 hover:border-brand-300 transition">
                <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600"><i class="fa-solid fa-list-ul"></i></span>
                <p class="font-display font-bold text-ink-900 mt-3">Columna 1</p>
                <p class="text-xs text-ink-500 mt-0.5">Enlaces tipo "Explorar"</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 mt-3 group-hover:gap-2 transition-all">Gestionar <i class="fa-solid fa-arrow-right text-[10px]"></i></span>
            </a>
            <a href="{{ route('admin.footer-column-two.index') }}" class="group bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 hover:border-brand-300 transition">
                <span class="grid place-items-center w-11 h-11 rounded-2xl bg-sun-100 text-sun-500"><i class="fa-solid fa-list-ul"></i></span>
                <p class="font-display font-bold text-ink-900 mt-3">Columna 2</p>
                <p class="text-xs text-ink-500 mt-0.5">Enlaces tipo "Soporte"</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 mt-3 group-hover:gap-2 transition-all">Gestionar <i class="fa-solid fa-arrow-right text-[10px]"></i></span>
            </a>
            <a href="{{ route('admin.social-links.index') }}" class="group bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 hover:border-brand-300 transition">
                <span class="grid place-items-center w-11 h-11 rounded-2xl bg-coral-100 text-coral-500"><i class="fa-solid fa-share-nodes"></i></span>
                <p class="font-display font-bold text-ink-900 mt-3">Redes sociales</p>
                <p class="text-xs text-ink-500 mt-0.5">Iconos de tus perfiles</p>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 mt-3 group-hover:gap-2 transition-all">Gestionar <i class="fa-solid fa-arrow-right text-[10px]"></i></span>
            </a>
        </section>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-3">
            <h3 class="font-display font-bold text-ink-900 text-sm">Visibilidad</h3>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setting->is_active)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Mostrar el pie de página</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="dark" value="1" @checked(old('dark', $setting->dark)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Fondo oscuro</span>
            </label>
            <p class="text-xs text-ink-400">El logo, el nombre del sitio y el copyright se editan en <a href="{{ route('admin.appearance.edit') }}" class="text-brand-700 underline">Apariencia</a>.</p>
        </section>

        <div class="space-y-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
            <a href="{{ url('/') }}" target="_blank" class="block text-center px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Ver el sitio</a>
        </div>
    </aside>
</form>

@endsection
