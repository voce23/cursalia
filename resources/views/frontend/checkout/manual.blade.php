@extends('layouts.app')

@section('title', 'Pago — '.$course->title)

@section('content')
@php
    $isQr = $method === 'qr';
    $val = fn ($k, $d = '') => $s[$k] ?? $d;
@endphp

<div class="max-w-2xl mx-auto px-4 py-10">
    <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center gap-2 text-sm text-ink-500 hover:text-ink-800 mb-5">
        <i class="fa-solid fa-arrow-left"></i> Volver al curso
    </a>

    <div class="rounded-3xl border border-ink-200 bg-white shadow-soft overflow-hidden">
        <div class="p-6 sm:p-8">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                {{ $isQr ? 'Pago con QR' : 'Transferencia bancaria' }}
            </p>
            <h1 class="font-display font-extrabold text-2xl text-ink-900 mt-1">{{ $course->title }}</h1>
            <p class="text-ink-600 mt-1">Monto a pagar: <strong class="text-ink-900 text-lg">${{ number_format($price, 2) }}</strong></p>

            {{-- Datos de pago --}}
            <div class="mt-6 rounded-2xl bg-cream-2/50 border border-ink-200/70 p-5">
                @if ($isQr)
                    <div class="text-center">
                        @if ($val('qr_image'))
                            <img src="{{ asset('storage/'.$val('qr_image')) }}" alt="QR de pago" class="w-56 h-56 object-contain mx-auto rounded-xl border border-ink-200 bg-white p-2">
                        @else
                            <p class="text-sm text-ink-500">El vendedor aún no cargó su QR.</p>
                        @endif
                        @if ($val('qr_holder'))
                            <p class="mt-3 text-sm text-ink-700"><i class="fa-solid fa-user"></i> {{ $val('qr_holder') }}</p>
                        @endif
                    </div>
                    @if ($val('qr_instructions'))
                        <p class="mt-4 text-sm text-ink-600 leading-relaxed">{{ $val('qr_instructions') }}</p>
                    @endif
                @else
                    <dl class="space-y-2 text-sm">
                        @if ($val('transfer_bank'))<div class="flex justify-between gap-4"><dt class="text-ink-500">Banco</dt><dd class="font-semibold text-ink-900 text-right">{{ $val('transfer_bank') }}</dd></div>@endif
                        @if ($val('transfer_account'))<div class="flex justify-between gap-4"><dt class="text-ink-500">Cuenta</dt><dd class="font-mono font-semibold text-ink-900 text-right">{{ $val('transfer_account') }}</dd></div>@endif
                        @if ($val('transfer_holder'))<div class="flex justify-between gap-4"><dt class="text-ink-500">Titular</dt><dd class="font-semibold text-ink-900 text-right">{{ $val('transfer_holder') }}</dd></div>@endif
                    </dl>
                    @if ($val('transfer_instructions'))
                        <p class="mt-4 text-sm text-ink-600 leading-relaxed">{{ $val('transfer_instructions') }}</p>
                    @endif
                @endif
            </div>

            {{-- Subir comprobante --}}
            <form method="POST" action="{{ route('checkout.manual.submit', [$course->slug, $method]) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1">Sube tu comprobante de pago <span class="text-coral-500">*</span></label>
                    <input type="file" name="proof" accept="image/*" required class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 file:font-semibold">
                    @error('proof')<p class="text-xs text-coral-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1">Referencia / nota (opcional)</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" maxlength="160" placeholder="Ej. N° de transacción o tu nombre" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold text-white shadow-soft transition hover:opacity-90" style="background:linear-gradient(135deg,#635BFF,#0070BA)">
                    <i class="fa-solid fa-paper-plane"></i> Enviar comprobante
                </button>
                <p class="text-center text-xs text-ink-400"><i class="fa-solid fa-clock"></i> Revisaremos tu pago y te daremos acceso al curso. Recibirás el acceso una vez aprobado.</p>
            </form>
        </div>
    </div>
</div>
@endsection
