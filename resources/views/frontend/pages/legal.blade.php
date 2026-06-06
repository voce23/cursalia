@extends('layouts.app')

@section('title', $title)
@section('description', $intro)

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     PÁGINA LEGAL Cursalia — plantilla común
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[24rem] h-[24rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[20rem] h-[20rem] top-20 right-0"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-ink-500 mb-5">
            <a href="{{ url('/') }}" class="hover:text-brand-700">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
            <span class="text-ink-900 font-medium">Legal</span>
            <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
            <span class="text-ink-700">{{ $title }}</span>
        </div>

        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <i class="fa-solid fa-scale-balanced"></i> Documento legal
        </span>
        <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight mt-5 text-ink-900">{{ $title }}</h1>
        <p class="text-ink-500 text-lg leading-relaxed mt-4 max-w-2xl">{{ $intro }}</p>

        <p class="text-xs text-ink-400 mt-6 inline-flex items-center gap-2">
            <i class="fa-regular fa-clock"></i>
            Última actualización: {{ $updated ?? now()->translatedFormat('d \d\e F, Y') }}
        </p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid lg:grid-cols-[1fr_220px] gap-10 items-start">

        {{-- Contenido --}}
        <article class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-10">
            <div class="prose prose-sm sm:prose-base max-w-none text-ink-700 prose-headings:font-display prose-headings:text-ink-900 prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-3 prose-a:text-brand-700 prose-strong:text-ink-900">
                {!! $body !!}
            </div>

            <hr class="my-8 border-ink-200/70">

            <div class="rounded-2xl bg-cream-2 border border-ink-200/70 p-5 flex items-start gap-3">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-brand-100 text-brand-600 shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <div class="flex-1">
                    <p class="font-display font-bold text-ink-900">¿Tienes dudas sobre este documento?</p>
                    <p class="text-sm text-ink-500 mt-1">Escríbenos y te respondemos en menos de 24 h.</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-full bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">
                        Contactar <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </article>

        {{-- Sidebar: otros documentos --}}
        <aside class="lg:sticky lg:top-24">
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Otros documentos</p>
                <ul class="space-y-1">
                    @foreach (($legalPages ?? []) as $p)
                        @php $isHere = trim(request()->path(), '/') === trim($p['url'], '/'); @endphp
                        <li>
                            <a href="{{ $p['url'] }}"
                               class="flex items-center justify-between px-3 py-2 rounded-2xl text-sm font-medium transition
                                      {{ $isHere ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                                {{ $p['title'] }}
                                @if ($isHere)<i class="fa-solid fa-circle text-[5px] text-brand-500"></i>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
</section>

@endsection
