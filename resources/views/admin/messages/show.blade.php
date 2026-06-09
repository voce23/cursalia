@extends('layouts.admin')

@section('title', 'Mensaje')
@section('page-title', 'Mensaje de contacto')

@section('content')

<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700 mb-5"><i class="fa-solid fa-arrow-left"></i> Mensajes</a>

    <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-ink-100">
            <div>
                <h2 class="font-display font-extrabold text-xl text-ink-900">{{ $message->subject ?: '(sin asunto)' }}</h2>
                <p class="text-sm text-ink-500 mt-1">
                    De <strong class="text-ink-700">{{ $message->name }}</strong> ·
                    <a href="mailto:{{ $message->email }}" class="text-brand-600 hover:underline">{{ $message->email }}</a>
                </p>
                <p class="text-xs text-ink-400 mt-1">{{ $message->created_at->format('d/m/Y H:i') }}@if($message->ip) · IP {{ $message->ip }}@endif</p>
            </div>
        </div>

        <div class="prose prose-sm max-w-none mt-5 text-ink-700 whitespace-pre-line">{{ $message->message }}</div>

        <div class="flex items-center gap-3 mt-7 pt-5 border-t border-ink-100">
            <a href="mailto:{{ $message->email }}?subject=RE: {{ $message->subject }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition"><i class="fa-solid fa-reply"></i> Responder por email</a>
            <form method="POST" action="{{ route('admin.messages.toggle', $message) }}">@csrf
                <button class="px-4 py-2.5 rounded-full bg-white border border-ink-200 text-ink-700 text-sm font-semibold hover:border-brand-300 transition">{{ $message->read_at ? 'Marcar como no leído' : 'Marcar como leído' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('¿Eliminar este mensaje?')" class="ml-auto">@csrf @method('DELETE')
                <button class="grid place-items-center w-10 h-10 rounded-full text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
    </div>
</div>

@endsection
