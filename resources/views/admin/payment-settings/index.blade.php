@extends('layouts.admin')

@section('title', 'Pasarelas de pago')
@section('page-title', 'Pasarelas de pago')
@section('page-subtitle', 'Cobra tus cursos con tarjeta, PayPal, QR o transferencia — enciende los que quieras')

@section('content')
@php($s = fn ($k, $d = '') => $settings[$k] ?? $d)
@php($on = fn ($k) => (string) ($settings[$k] ?? '') === '1')

@if (! $paymentsActive)
    {{-- ───── NO ACTIVO: pedir la llave ───── --}}
    <div class="max-w-2xl">
        <div class="rounded-3xl border border-ink-200 bg-white p-8 text-center shadow-soft">
            <div class="w-16 h-16 rounded-full grid place-items-center mx-auto text-white text-3xl mb-4" style="background:linear-gradient(135deg,#635BFF,#0070BA)">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <h2 class="font-display font-extrabold text-2xl text-ink-900">Activa las pasarelas de pago</h2>
            <p class="text-ink-600 mt-2">Cobra tus cursos con <strong>tarjeta (Stripe)</strong>, <strong>PayPal</strong>, <strong>QR</strong> y <strong>transferencia bancaria</strong>. Enciende solo los métodos que necesites.</p>

            <form method="POST" action="{{ route('admin.payment-settings.activate') }}" class="mt-6 max-w-md mx-auto">
                @csrf
                <label class="block text-sm font-semibold text-ink-700 mb-1 text-left">Llave de activación</label>
                <input type="text" name="payments_key" value="{{ old('payments_key') }}" placeholder="PAY-XXXXXXXX-XXXX"
                       class="w-full px-4 py-3 rounded-xl border border-ink-200 font-mono uppercase text-center focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition">
                @error('payments_key')<p class="text-xs text-coral-600 mt-1 text-left">{{ $message }}</p>@enderror
                <button type="submit" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-extrabold text-white" style="background:linear-gradient(135deg,#635BFF,#0070BA)">
                    <i class="fa-solid fa-key"></i> Activar pasarelas
                </button>
                <p class="mt-3 text-xs text-ink-400">¿No tienes tu llave? <a href="https://cursalia.org/tienda" target="_blank" rel="noopener" class="text-indigo-600 font-semibold hover:underline">Consíguela en la tienda</a>.</p>
            </form>
        </div>
    </div>
@else
    {{-- ───── ACTIVO: 4 métodos con interruptores ───── --}}
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
        <p class="text-sm text-green-800 font-semibold">Pasarelas activadas. Enciende y configura cada método abajo. El alumno verá solo los que tengas <strong>encendidos</strong>.</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- ═══ STRIPE ═══ --}}
        <div class="rounded-3xl border {{ $on('stripe_enabled') ? 'border-green-300' : 'border-ink-200' }} bg-white p-7 shadow-soft">
            <form method="POST" action="{{ route('admin.payment-settings.stripe') }}" class="space-y-3">
                @csrf
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                        <i class="fa-brands fa-stripe text-2xl" style="color:#635BFF"></i>
                        <h3 class="font-display font-bold text-lg text-ink-900">Tarjeta (Stripe)</h3>
                    </div>
                    @include('admin.payment-settings._toggle', ['name' => 'stripe_enabled', 'checked' => $on('stripe_enabled')])
                </div>
                <p class="text-xs text-ink-500 mb-2">Cobro automático con tarjeta. Claves en dashboard.stripe.com → Desarrolladores → Claves de API.</p>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Clave publicable (pk_...)</label>
                    <input type="text" name="stripe_publishable_key" value="{{ $s('stripe_publishable_key') }}" placeholder="pk_test_..." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Clave secreta (sk_...)</label>
                    <input type="password" name="stripe_secret" value="{{ $s('stripe_secret') }}" placeholder="sk_test_..." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Moneda</label>
                    <input type="text" name="stripe_currency" value="{{ $s('stripe_currency', 'USD') }}" maxlength="3" class="w-32 px-4 py-2.5 rounded-xl border border-ink-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white" style="background:#635BFF">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Stripe
                </button>
            </form>
        </div>

        {{-- ═══ PAYPAL ═══ --}}
        <div class="rounded-3xl border {{ $on('paypal_enabled') ? 'border-green-300' : 'border-ink-200' }} bg-white p-7 shadow-soft">
            <form method="POST" action="{{ route('admin.payment-settings.paypal') }}" class="space-y-3">
                @csrf
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                        <i class="fa-brands fa-paypal text-2xl" style="color:#0070BA"></i>
                        <h3 class="font-display font-bold text-lg text-ink-900">PayPal</h3>
                    </div>
                    @include('admin.payment-settings._toggle', ['name' => 'paypal_enabled', 'checked' => $on('paypal_enabled')])
                </div>
                <p class="text-xs text-ink-500 mb-2">Cobro automático con PayPal. Claves en developer.paypal.com → Apps & Credentials.</p>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Modo</label>
                    <select name="paypal_mode" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                        <option value="sandbox" @selected($s('paypal_mode','sandbox')==='sandbox')>Pruebas (sandbox)</option>
                        <option value="live" @selected($s('paypal_mode')==='live')>Real (live)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Client ID</label>
                    <input type="text" name="paypal_client_id" value="{{ $s('paypal_client_id') }}" placeholder="A..." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Client Secret</label>
                    <input type="password" name="paypal_client_secret" value="{{ $s('paypal_client_secret') }}" placeholder="E..." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Moneda</label>
                    <input type="text" name="paypal_currency" value="{{ $s('paypal_currency', 'USD') }}" maxlength="3" class="w-32 px-4 py-2.5 rounded-xl border border-ink-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white" style="background:#0070BA">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar PayPal
                </button>
            </form>
        </div>

        {{-- ═══ QR ═══ --}}
        <div class="rounded-3xl border {{ $on('qr_enabled') ? 'border-green-300' : 'border-ink-200' }} bg-white p-7 shadow-soft">
            <form method="POST" action="{{ route('admin.payment-settings.qr') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-qrcode text-2xl text-ink-800"></i>
                        <h3 class="font-display font-bold text-lg text-ink-900">QR</h3>
                    </div>
                    @include('admin.payment-settings._toggle', ['name' => 'qr_enabled', 'checked' => $on('qr_enabled')])
                </div>
                <p class="text-xs text-ink-500 mb-2">Pago manual: el alumno escanea tu QR y <strong>sube su comprobante</strong>; tú lo apruebas en “Ventas”.</p>
                @if ($s('qr_image'))
                    <img src="{{ asset('storage/'.$s('qr_image')) }}" alt="QR" class="w-32 h-32 object-contain rounded-xl border border-ink-200 bg-white p-1">
                @endif
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Imagen de tu QR</label>
                    <input type="file" name="qr_image" accept="image/*" class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 file:font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Titular / Banco</label>
                    <input type="text" name="qr_holder" value="{{ $s('qr_holder') }}" placeholder="Ej. Eusebio Panozo · BNB" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Instrucciones (opcional)</label>
                    <textarea name="qr_instructions" rows="2" placeholder="Ej. Escanea el QR, paga el monto exacto y sube la captura." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">{{ $s('qr_instructions') }}</textarea>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white bg-ink-800 hover:bg-ink-900">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar QR
                </button>
            </form>
        </div>

        {{-- ═══ TRANSFERENCIA ═══ --}}
        <div class="rounded-3xl border {{ $on('transfer_enabled') ? 'border-green-300' : 'border-ink-200' }} bg-white p-7 shadow-soft">
            <form method="POST" action="{{ route('admin.payment-settings.transfer') }}" class="space-y-3">
                @csrf
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-2xl text-emerald-600"></i>
                        <h3 class="font-display font-bold text-lg text-ink-900">Transferencia bancaria</h3>
                    </div>
                    @include('admin.payment-settings._toggle', ['name' => 'transfer_enabled', 'checked' => $on('transfer_enabled')])
                </div>
                <p class="text-xs text-ink-500 mb-2">Pago manual: el alumno transfiere y <strong>sube su comprobante</strong>; tú lo apruebas en “Ventas”.</p>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Banco</label>
                    <input type="text" name="transfer_bank" value="{{ $s('transfer_bank') }}" placeholder="Ej. Banco Nacional de Bolivia" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Número de cuenta</label>
                    <input type="text" name="transfer_account" value="{{ $s('transfer_account') }}" placeholder="Ej. 1000-12345678" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Titular</label>
                    <input type="text" name="transfer_holder" value="{{ $s('transfer_holder') }}" placeholder="Ej. Eusebio Panozo" class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-700 mb-1">Instrucciones (opcional)</label>
                    <textarea name="transfer_instructions" rows="2" placeholder="Ej. Transfiere el monto y sube el comprobante." class="w-full px-4 py-2.5 rounded-xl border border-ink-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">{{ $s('transfer_instructions') }}</textarea>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white bg-emerald-600 hover:bg-emerald-700">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar transferencia
                </button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-xs text-ink-400"><i class="fa-solid fa-shield-halved"></i> Tus claves secretas se guardan cifradas. Empieza siempre en modo “pruebas/sandbox” antes de cobrar de verdad. Los pagos por QR y transferencia se aprueban en <a href="{{ route('admin.course-orders.index') }}" class="text-indigo-600 font-semibold hover:underline">Ventas</a>.</p>
@endif
@endsection
