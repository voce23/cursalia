@php($course = $course ?? null)

@if ($errors->any())
    <div class="mb-6 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-5">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-circle-info text-brand-500"></i> Información del curso</h3>

            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Título <span class="text-coral-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $course?->title) }}" required maxlength="255"
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Descripción <span class="text-coral-500">*</span></label>
                <textarea name="description" rows="6" required
                          class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">{{ old('description', $course?->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Descripción SEO (meta)</label>
                <input type="text" name="seo_description" value="{{ old('seo_description', $course?->seo_description) }}" maxlength="255"
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
        </div>

        {{-- Vídeo de presentación --}}
        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-5">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-circle-play text-brand-500"></i> Vídeo de presentación (opcional)</h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Fuente</label>
                    <select name="demo_video_storage" class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                        <option value="">— Ninguno —</option>
                        @foreach (['youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'external_link' => 'Enlace externo'] as $v => $label)
                            <option value="{{ $v }}" @selected(old('demo_video_storage', $course?->demo_video_source ? $course?->demo_video_storage : null) === $v)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">URL / ID del vídeo</label>
                    <input type="text" name="demo_video_source" value="{{ old('demo_video_source', $course?->demo_video_source) }}" maxlength="1000" placeholder="https://…"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>
            </div>
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="space-y-6">
        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-image text-brand-500"></i> Portada <span class="text-coral-500">*</span></h3>
            @if ($course?->thumbnail)
                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="" class="w-full h-32 rounded-xl object-cover border border-ink-200">
            @endif
            <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" {{ $course?->thumbnail ? '' : 'required' }}
                   class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-brand-700 file:font-semibold hover:file:bg-brand-100 cursor-pointer">
            <p class="text-xs text-ink-400">JPG, PNG o WebP · 600×400 recomendado.</p>
        </div>

        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-sliders text-brand-500"></i> Clasificación</h3>
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Instructor <span class="text-coral-500">*</span></label>
                <select name="instructor_id" required class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                    <option value="">— Elige —</option>
                    @foreach ($instructors as $i)
                        <option value="{{ $i->id }}" @selected(old('instructor_id', $course?->instructor_id) == $i->id)>{{ $i->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Categoría <span class="text-coral-500">*</span></label>
                <select name="category_id" required class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                    <option value="">— Elige —</option>
                    @foreach ($categories as $cat)
                        @if ($cat->subcategories->isNotEmpty())
                            <optgroup label="{{ $cat->name }}">
                                @foreach ($cat->subcategories as $sub)
                                    <option value="{{ $sub->id }}" @selected(old('category_id', $course?->category_id) == $sub->id)>{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Nivel <span class="text-coral-500">*</span></label>
                    <select name="course_level_id" required class="w-full rounded-xl border border-ink-200 px-3 py-2.5 text-sm focus:border-brand-400 outline-none">
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->id }}" @selected(old('course_level_id', $course?->course_level_id) == $lv->id)>{{ $lv->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Idioma <span class="text-coral-500">*</span></label>
                    <select name="course_language_id" required class="w-full rounded-xl border border-ink-200 px-3 py-2.5 text-sm focus:border-brand-400 outline-none">
                        @foreach ($languages as $lg)
                            <option value="{{ $lg->id }}" @selected(old('course_language_id', $course?->course_language_id) == $lg->id)>{{ $lg->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Duración <span class="text-coral-500">*</span></label>
                <input type="text" name="duration" value="{{ old('duration', $course?->duration) }}" required maxlength="100" placeholder="Ej. 5h 30m"
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-tag text-brand-500"></i> Precio</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Precio (€) <span class="text-coral-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $course?->price ?? 0) }}" required
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                    <p class="text-[11px] text-ink-400 mt-1">0 = gratis</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Descuento (€)</label>
                    <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', $course?->discount) }}"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                </div>
            </div>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="certificate" value="1" @checked(old('certificate', $course?->certificate)) class="w-4 h-4 rounded border-ink-300 text-brand-600 focus:ring-brand-200">
                <span class="text-sm text-ink-700">Emitir certificado al completar</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="qna" value="1" @checked(old('qna', $course?->qna)) class="w-4 h-4 rounded border-ink-300 text-brand-600 focus:ring-brand-200">
                <span class="text-sm text-ink-700">Permitir preguntas y respuestas</span>
            </label>
        </div>

        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 space-y-3">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-eye text-brand-500"></i> Visibilidad</h3>
            <select name="status" class="w-full rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 outline-none">
                <option value="active" @selected(old('status', $course?->status ?? 'active') === 'active')>Publicado (visible en la web)</option>
                <option value="draft" @selected(old('status', $course?->status) === 'draft')>Borrador (oculto)</option>
            </select>
            <p class="text-xs text-ink-400">Un curso "Publicado" aparece en el catálogo. Ponlo en "Borrador" mientras lo preparas.</p>
        </div>
    </div>
</div>

<div class="flex items-center gap-3 mt-6">
    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-brand-600 text-white font-bold shadow-soft hover:bg-brand-700 transition">
        <i class="fa-solid fa-check"></i> {{ $course?->exists ? 'Guardar cambios' : 'Crear curso' }}
    </button>
    <a href="{{ route('admin.courses.index') }}" class="px-6 py-3 rounded-full text-ink-600 font-semibold hover:bg-ink-100 transition">Cancelar</a>
</div>
