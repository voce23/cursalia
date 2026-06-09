@extends('layouts.admin')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter')
@section('page-subtitle', 'Suscriptores y envío de novedades')

@section('content')

<div class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">

    {{-- Lista de suscriptores --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-ink-700">
                <i class="fa-solid fa-users text-brand-600"></i> {{ $subscriberCount }} {{ \Illuminate\Support\Str::plural('suscriptor', $subscriberCount) }}
            </span>
        </div>

        @if ($subscribers->isNotEmpty())
            <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-cream-2 text-ink-500 text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold hidden sm:table-cell">Fecha</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @foreach ($subscribers as $sub)
                            <tr class="hover:bg-cream-2/50">
                                <td class="px-4 py-3 font-medium text-ink-800">{{ $sub->email }}</td>
                                <td class="px-4 py-3 hidden sm:table-cell text-ink-400 text-xs">{{ $sub->created_at?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="mailto:{{ $sub->email }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Escribir"><i class="fa-regular fa-envelope text-xs"></i></a>
                                        <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('¿Eliminar este suscriptor?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $subscribers->links() }}</div>
        @else
            <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
                <i class="fa-solid fa-envelope-open-text text-3xl text-ink-300"></i>
                <p class="font-display font-bold text-ink-900 mt-4">Aún no hay suscriptores</p>
                <p class="text-sm text-ink-500 mt-1">Aparecerán aquí cuando alguien use el formulario de novedades del sitio.</p>
            </div>
        @endif
    </div>

    {{-- Enviar newsletter --}}
    <aside class="lg:sticky lg:top-24">
        <form method="POST" action="{{ route('admin.newsletter.send') }}" class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            @csrf
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2"><i class="fa-regular fa-paper-plane text-coral-500"></i> Enviar a todos</h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Asunto</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('subject')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Mensaje</label>
                <textarea name="body" rows="8" required minlength="10"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y">{{ old('body') }}</textarea>
                @error('body')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <button type="submit" onclick="return confirm('¿Enviar este correo a los {{ $subscriberCount }} suscriptores?')"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-paper-plane"></i> Enviar newsletter
            </button>
            <p class="text-xs text-ink-400">Se envía en segundo plano. Necesitas tener el correo (SMTP) configurado.</p>
        </form>
    </aside>
</div>

@endsection
