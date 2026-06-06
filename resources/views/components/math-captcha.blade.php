{{--
   Componente reusable de captcha matemático anti-spam.

   Uso:
       <x-math-captcha />

   Variantes:
       <x-math-captcha label="Para comentar, demuéstranos que eres humano:" />

   Genera dos inputs: captcha_token (hidden cifrado) y captcha_answer (number).
--}}
@props([
    'label' => 'Antes de enviar, ¿cuánto es esto?',
])

@php $captcha = \App\Helpers\MathCaptcha::generate(); @endphp

<div class="rounded-2xl bg-cream-2 border border-ink-200 p-4">
    <input type="hidden" name="captcha_token" value="{{ $captcha['token'] }}">

    <label class="flex items-center gap-3 text-sm">
        <span class="grid place-items-center w-9 h-9 rounded-xl bg-brand-100 text-brand-600 shrink-0">
            <i class="fa-solid fa-shield-halved"></i>
        </span>
        <span class="flex-1">
            <span class="block text-xs font-semibold uppercase tracking-wider text-ink-400 mb-0.5">{{ $label }}</span>
            <span class="font-display font-extrabold text-ink-900 text-lg">{{ $captcha['question'] }} = ?</span>
        </span>
        <input type="number" name="captcha_answer" required step="1" placeholder="?"
            class="w-20 px-3 py-2 rounded-xl bg-white border-2 border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-center font-display font-extrabold text-ink-900 text-lg @error('captcha_answer') border-coral-400 ring-2 ring-coral-200 @enderror">
    </label>

    @error('captcha_answer')
        <p class="text-xs text-coral-500 mt-2 ml-12 flex items-center gap-1.5">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $message }}
        </p>
    @enderror
</div>
