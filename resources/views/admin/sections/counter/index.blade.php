@extends('layouts.admin')

@section('title', 'Cifras / estadísticas')
@section('page-title', 'Cifras / estadísticas')
@section('page-subtitle', 'Los 4 números de la sección "Cifras" en la página "Nosotros"')

@section('content')

<form method="POST" action="{{ route('admin.counter.update') }}" class="max-w-3xl space-y-6">
    @csrf

    <div class="rounded-2xl bg-brand-50 border border-brand-200 text-brand-800 text-sm px-4 py-3 flex items-start gap-2">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        <span>El "valor" es un número entero (ej. <strong>500</strong>, <strong>1200</strong>). La "etiqueta" es el texto que lo acompaña (ej. <strong>Estudiantes</strong>). Una cifra con valor <strong>0</strong> o vacío no se muestra.</span>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach (['one' => 'Cifra 1', 'two' => 'Cifra 2', 'three' => 'Cifra 3', 'four' => 'Cifra 4'] as $key => $label)
            <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-3">
                <p class="font-display font-bold text-ink-900 text-sm">{{ $label }}</p>
                <div>
                    <label class="block text-xs text-ink-500 mb-1">Valor</label>
                    <input type="number" min="0" name="counter_{{ $key }}_value" value="{{ old('counter_'.$key.'_value', $counter->{'counter_'.$key.'_value'}) }}" placeholder="500"
                        class="w-full px-4 py-2.5 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-ink-500 mb-1">Etiqueta</label>
                    <input type="text" name="counter_{{ $key }}_title" value="{{ old('counter_'.$key.'_title', $counter->{'counter_'.$key.'_title'}) }}" maxlength="120" placeholder="Estudiantes"
                        class="w-full px-4 py-2.5 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
            </section>
        @endforeach
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
            <i class="fa-solid fa-floppy-disk"></i> Guardar cifras
        </button>
        <a href="{{ route('about') }}" target="_blank" class="px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Ver "Nosotros"</a>
    </div>
</form>

@endsection
