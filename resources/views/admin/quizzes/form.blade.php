@extends('layouts.admin')

@section('title', $quiz->exists ? 'Editar autoevaluación' : 'Nueva autoevaluación')
@section('page-title', $quiz->exists ? 'Editar autoevaluación' : 'Nueva autoevaluación')
@section('page-subtitle', 'Lección: ' . $lesson->title)

@section('content')

@if ($errors->any())
    <div class="mb-5 px-4 py-3 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 text-sm">
        <i class="fa-solid fa-circle-exclamation"></i> Revisa los campos:
        <ul class="list-disc ml-5 mt-1">
            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@php
    // Preparar datos iniciales para Alpine (preguntas existentes o 1 vacía).
    $initialQuestions = $quiz->exists && $quiz->questions->isNotEmpty()
        ? $quiz->questions->map(fn ($q) => [
            'question'    => $q->question,
            'type'        => $q->question_type,
            'explanation' => $q->explanation,
            'correct'     => $q->options->search(fn ($o) => $o->is_correct) ?: 0,
            'options'     => $q->options->map(fn ($o) => ['text' => $o->option_text])->values()->all(),
        ])->values()->all()
        : [[
            'question' => '', 'type' => 'multiple_choice', 'explanation' => '',
            'correct' => 0, 'options' => [['text' => ''], ['text' => '']],
        ]];
@endphp

<form method="POST"
      action="{{ $quiz->exists ? route('admin.quizzes.update', $quiz->id) : route('admin.quizzes.store') }}"
      x-data="quizBuilder(@js($initialQuestions))">
    @csrf
    @if ($quiz->exists) @method('PUT') @endif
    <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Columna principal: preguntas --}}
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-ink-800 mb-1">Título</label>
                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255"
                           class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-800 mb-1">Descripción <span class="text-ink-400 font-normal">(opcional)</span></label>
                    <input type="text" name="description" value="{{ old('description', $quiz->description) }}" maxlength="1000"
                           class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 text-sm">
                </div>
            </div>

            {{-- Preguntas --}}
            <template x-for="(q, qi) in questions" :key="qi">
                <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600">
                            Pregunta <span x-text="qi + 1"></span>
                        </span>
                        <button type="button" @click="removeQuestion(qi)" x-show="questions.length > 1"
                                class="text-coral-500 hover:text-coral-700 text-sm"><i class="fa-solid fa-trash"></i></button>
                    </div>

                    <input type="text" :name="`questions[${qi}][question]`" x-model="q.question" required maxlength="1000"
                           placeholder="Escribe la pregunta…"
                           class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 text-sm mb-3">

                    <div class="flex items-center gap-3 mb-3">
                        <label class="text-xs font-semibold text-ink-600">Tipo:</label>
                        <select :name="`questions[${qi}][type]`" x-model="q.type" @change="onTypeChange(qi)"
                                class="px-3 py-1.5 rounded-xl bg-cream-2 border border-ink-200 text-sm">
                            <option value="multiple_choice">Opción múltiple</option>
                            <option value="true_false">Verdadero / Falso</option>
                        </select>
                    </div>

                    {{-- Opciones --}}
                    <p class="text-xs font-semibold text-ink-600 mb-1.5">Opciones <span class="text-ink-400 font-normal">(marca la correcta)</span></p>
                    <div class="space-y-2">
                        <template x-for="(opt, oi) in q.options" :key="oi">
                            <div class="flex items-center gap-2">
                                <input type="radio" :name="`questions[${qi}][correct]`" :value="oi" x-model.number="q.correct" required
                                       class="w-4 h-4 text-brand-500 focus:ring-brand-400 shrink-0">
                                <input type="text" :name="`questions[${qi}][options][${oi}][text]`" x-model="opt.text" required maxlength="500"
                                       placeholder="Texto de la opción…"
                                       class="flex-1 px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:border-brand-400 text-sm">
                                <button type="button" @click="removeOption(qi, oi)" x-show="q.type === 'multiple_choice' && q.options.length > 2"
                                        class="text-ink-300 hover:text-coral-500"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addOption(qi)" x-show="q.type === 'multiple_choice'"
                            class="mt-2 text-xs font-semibold text-brand-600 hover:text-brand-700">
                        <i class="fa-solid fa-plus"></i> Añadir opción
                    </button>

                    {{-- Explicación --}}
                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-ink-600 mb-1">Explicación <span class="text-ink-400 font-normal">(se muestra al corregir)</span></label>
                        <input type="text" :name="`questions[${qi}][explanation]`" x-model="q.explanation" maxlength="1000"
                               placeholder="Por qué esta es la respuesta correcta…"
                               class="w-full px-4 py-2 rounded-2xl bg-cream-2 border border-ink-200 focus:border-brand-400 text-sm">
                    </div>
                </div>
            </template>

            <button type="button" @click="addQuestion()"
                    class="w-full py-3 rounded-2xl border-2 border-dashed border-brand-300 text-brand-600 font-semibold text-sm hover:bg-brand-50 transition">
                <i class="fa-solid fa-plus"></i> Añadir pregunta
            </button>
        </div>

        {{-- Sidebar: ajustes --}}
        <div class="space-y-5">
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4 sticky top-24">
                <h3 class="font-display font-bold text-ink-900">Ajustes</h3>

                <div>
                    <label class="block text-sm font-semibold text-ink-800 mb-1">% para aprobar</label>
                    <input type="number" name="passing_score" min="0" max="100" value="{{ old('passing_score', $quiz->passing_score ?? 70) }}" required
                           class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-ink-800 mb-1">Máx. intentos <span class="text-ink-400 font-normal">(0 = ilimitado)</span></label>
                    <input type="number" name="max_attempts" min="0" max="50" value="{{ old('max_attempts', $quiz->max_attempts ?? 3) }}" required
                           class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 text-sm">
                </div>

                <label class="flex items-center gap-2.5 text-sm text-ink-700">
                    <input type="checkbox" name="allow_retakes" value="1" {{ old('allow_retakes', $quiz->allow_retakes ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-500 focus:ring-brand-400">
                    Permitir reintentos
                </label>

                <label class="flex items-center gap-2.5 text-sm text-ink-700">
                    <input type="checkbox" name="status" value="1" {{ old('status', $quiz->status ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-500 focus:ring-brand-400">
                    Activa (visible para alumnos)
                </label>

                <button type="submit" class="w-full py-3 rounded-2xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-soft transition">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
                <a href="{{ route('admin.quizzes.index') }}" class="block text-center text-sm text-ink-500 hover:text-ink-700">Cancelar</a>
            </div>
        </div>
    </div>
</form>

<script>
function quizBuilder(initial) {
    return {
        questions: initial,
        addQuestion() {
            this.questions.push({
                question: '', type: 'multiple_choice', explanation: '',
                correct: 0, options: [{ text: '' }, { text: '' }],
            });
        },
        removeQuestion(qi) { this.questions.splice(qi, 1); },
        addOption(qi) { this.questions[qi].options.push({ text: '' }); },
        removeOption(qi, oi) {
            this.questions[qi].options.splice(oi, 1);
            if (this.questions[qi].correct >= this.questions[qi].options.length) {
                this.questions[qi].correct = 0;
            }
        },
        onTypeChange(qi) {
            if (this.questions[qi].type === 'true_false') {
                this.questions[qi].options = [{ text: 'Verdadero' }, { text: 'Falso' }];
                this.questions[qi].correct = 0;
            }
        },
    };
}
</script>

@endsection
