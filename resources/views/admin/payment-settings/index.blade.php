@extends('layouts.admin')

@section('title', 'Pagos')
@section('page-title', 'Pagos internacionales')
@section('page-subtitle', 'Cobra con tarjeta (Stripe) y PayPal a alumnos de todo el mundo — sin tocar código')

@section('content')
@php($s = fn ($k, $d = '') => $settings[$k] ?? $d)

@if (! $paymentsActive)
    {{-- ───── NO ACTIVO: pedir la llave ───── --}}
    <div class="max-w-2xl">
        <div class="rounded-3xl border border-ink-200 bg-white p-8 text-center shadow-soft">
            <div class="w-16 h-16 rounded-full grid place-items-center mx-auto text-white text-3xl mb-4" style="background:linear-gradient(135deg,#635BFF,#0070BA)">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <h2 class="font-display font-extrabold text-2xl text-ink-900">Activa los pagos internacionales</h2>
            <p class="text-ink-600 mt-2">Con este complemento podrás cobrar tus cursos con <strong>tarjeta (Stripe)</strong> y <strong>PayPal</strong> a alumnos de cualquier país.</p>

            <form method="POST" action="{{ route('admin.payment-settings.activate') }}" class="mt-6 max-w-md mx-auto">
                @csrf
                <label class="block text-sm font-semibold text-ink-700 mb-1 text-left">Llave de activación</label>
                <input type="text" name="payments_key" value="{{ old('payments_key') }}" placeholder="PAY-XXXXXXXX-XXXX"
                       class="w-full px-4 py-3 rounded-xl border border-ink-200 font-mono uppercase text-center focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition">
                @error('payments_key')<p class="text-xs text-coral-600 mt-1 text-left">{{ $message }}</p>@enderror
                <button type="submit" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-extrabold text-white" style="background:linear-gradient(135deg,#635BFF,#0070BA)">
                    <i class="fa-solid fa-key"></i> Activar pagos
                </button>
                <p class="mt-3 text-xs text-ink-400">¿No tienes tu llave? <a href="https://cursalia.org/tienda" target="_blank" rel="noopener" class="text-indigo-600 font-semibold hover:underline">Consíguela en la tienda</a>.</p>
            </form>
        </div>
    </div>
@else
    {{-- ───── ACTIVO: configurar Stripe + PayPal ───── --}}
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
        <p class="text-sm text-green-800 font-semibold">Pagos internacionales activados. Configura tus pasarelas abajo.</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- STRIPE --}}
        <div class="rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-brands fa-stripe text-2xl" style="color:#635BFF"></i>
                <h3 class="font-display font-bold text-lg text-ink-900">Stripe (tarjeta)</h3>
            </div>
            <p class="text-xs text-ink-500 mb-4">Cobra con tarjeta de crédito/débito. Obtén tus claves en dashboard.stripe.com → Desarrolladores → Claves de API.</p>
            <form method="POST" action="{{ route('admin.payment-settings.stripe') }}" class="space-y-3">
                @csrf
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
                    <input type="text" name="stripe_currency" value="{{ $s('stripe_currency', 'USD') }}" maxlength="3" placeholder="USD" class="w-32 px-4 py-2.5 rounded-xl border border-ink-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white" style="background:#635BFF">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Stripe
                </button>
            </form>
        </div>

        {{-- PAYPAL --}}
        <div class="rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-brands fa-paypal text-2xl" style="color:#0070BA"></i>
                <h3 class="font-display font-bold text-lg text-ink-900">PayPal</h3>
            </div>
            <p class="text-xs text-ink-500 mb-4">Cobra por PayPal. Obtén tus claves en developer.paypal.com → Apps & Credentials.</p>
            <form method="POST" action="{{ route('admin.payment-settings.paypal') }}" class="space-y-3">
                @csrf
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
                    <input type="text" name="paypal_currency" value="{{ $s('paypal_currency', 'USD') }}" maxlength="3" placeholder="USD" class="w-32 px-4 py-2.5 rounded-xl border border-ink-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-white" style="background:#0070BA">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar PayPal
                </button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-xs text-ink-400"><i class="fa-solid fa-shield-halved"></i> Tus claves secretas se guardan cifradas. Empieza siempre en modo "pruebas/sandbox" antes de cobrar de verdad.</p>
@endif
@endsection
