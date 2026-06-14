@php($lesson = $lesson ?? null)
<div class="grid sm:grid-cols-2 gap-3">
    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-ink-600 mb-1">Título de la lección <span class="text-coral-500">*</span></label>
        <input type="text" name="title" value="{{ $lesson?->title }}" required maxlength="255"
               class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-brand-400 outline-none">
    </div>

    <div>
        <label class="block text-xs font-semibold text-ink-600 mb-1">Fuente del vídeo</label>
        <select name="storage" x-model="storage" class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-brand-400 outline-none">
            <option value="external_link">Enlace externo (Bunny.net, MP4…)</option>
            <option value="youtube">YouTube</option>
            <option value="vimeo">Vimeo</option>
            <option value="upload">Subir archivo (ocupa espacio del hosting)</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-ink-600 mb-1">Tipo</label>
        <select name="file_type" class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-brand-400 outline-none">
            @foreach (['video' => 'Vídeo', 'audio' => 'Audio', 'pdf' => 'PDF', 'doc' => 'Documento', 'file' => 'Archivo'] as $v => $label)
                <option value="{{ $v }}" @selected(($lesson?->file_type ?? 'video') === $v)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- URL (Bunny.net / youtube / vimeo / enlace) --}}
    <div class="sm:col-span-2" x-show="storage !== 'upload'">
        <label class="block text-xs font-semibold text-ink-600 mb-1">URL del vídeo</label>
        <input type="text" name="file_path" value="{{ $lesson && $lesson->storage !== 'upload' ? $lesson->file_path : '' }}" maxlength="2000"
               placeholder="Pega aquí el enlace de Bunny.net o YouTube…"
               class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-brand-400 outline-none">
        <p class="text-[11px] text-ink-400 mt-1">Recomendado: aloja tus vídeos en <strong>Bunny.net</strong> (enlace externo) o <strong>YouTube</strong> y pega aquí el enlace. Así no ocupan espacio de tu hosting.</p>
    </div>

    {{-- Subida de archivo --}}
    <div class="sm:col-span-2" x-show="storage === 'upload'" x-cloak>
        <label class="block text-xs font-semibold text-ink-600 mb-1">Archivo (vídeo, audio, PDF…)</label>
        @if ($lesson && $lesson->storage === 'upload' && $lesson->file_path)
            <p class="text-xs text-ink-400 mb-1">Actual: {{ basename($lesson->file_path) }} (sube uno nuevo para reemplazar)</p>
        @endif
        <input type="file" name="file" class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-brand-700 file:font-semibold hover:file:bg-brand-100 cursor-pointer">
        <p class="text-[11px] text-ink-400 mt-1">Máximo 200 MB.</p>
    </div>

    <div>
        <label class="block text-xs font-semibold text-ink-600 mb-1">Duración</label>
        <input type="text" name="duration" value="{{ $lesson?->duration }}" maxlength="20" placeholder="08:30"
               class="w-full rounded-lg border border-ink-200 px-3 py-2 text-sm focus:border-brand-400 outline-none">
    </div>
    <div class="flex items-center gap-4 pt-6">
        <label class="inline-flex items-center gap-1.5 text-xs text-ink-700 cursor-pointer">
            <input type="checkbox" name="is_preview" value="1" @checked($lesson?->is_preview) class="rounded border-ink-300 text-brand-600">
            Vista previa gratis
        </label>
        <label class="inline-flex items-center gap-1.5 text-xs text-ink-700 cursor-pointer">
            <input type="checkbox" name="downloadable" value="1" @checked($lesson?->downloadable) class="rounded border-ink-300 text-brand-600">
            Descargable
        </label>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-ink-600 mb-1">Descripción · texto que se muestra bajo el video</label>
        <x-content-editor name="description" :value="$lesson?->description ?? ''" :rows="6" />
    </div>
</div>
