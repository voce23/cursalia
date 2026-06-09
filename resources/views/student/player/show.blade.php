<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentLesson->title ?? $course->title }} · Cursalia</title>
    <meta name="robots" content="noindex,nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310B981'><path d='M4 7l8-4 8 4-8 4-8-4z'/></svg>">
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen" x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

    {{-- ════════ Sidebar · Curriculum ════════ --}}
    <aside class="bg-white border-r border-ink-200/70 flex-shrink-0 transition-all duration-300"
           :class="sidebarOpen ? 'w-80' : 'w-0 overflow-hidden'">
        <div class="h-screen sticky top-0 flex flex-col">
            {{-- Header sidebar --}}
            <div class="p-5 border-b border-ink-200/70">
                <p class="text-[11px] uppercase tracking-wider text-ink-400 font-semibold">Contenido del curso</p>
                <h2 class="text-sm font-display font-bold text-ink-900 mt-1 line-clamp-2">{{ $course->title }}</h2>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-ink-500 mb-1.5">
                        <span>{{ count($completedLessonIds) }}/{{ $totalLessons }} lecciones</span>
                        <span class="font-semibold text-brand-600" id="sidebar-progress-pct">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-cream-2 rounded-full h-2 overflow-hidden">
                        <div id="sidebar-progress-bar" class="h-2 rounded-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all duration-500"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Lista de capítulos --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                @forelse ($chapters as $chapter)
                    @php $isChapterActive = $chapter->lessons->contains('id', optional($currentLesson)->id); @endphp
                    <div x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-3 rounded-2xl text-left transition
                                       {{ $isChapterActive ? 'bg-brand-50 border border-brand-200' : 'bg-cream-2 border border-ink-200/70 hover:bg-ink-50' }}">
                            <span class="text-xs font-bold {{ $isChapterActive ? 'text-brand-700' : 'text-ink-600' }}">
                                {{ $chapter->title }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-ink-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <ul x-show="open" x-collapse class="mt-1 space-y-0.5 pl-1.5">
                            @forelse ($chapter->lessons as $lesson)
                                @php
                                    $isActive    = optional($currentLesson)->id === $lesson->id;
                                    $isCompleted = in_array($lesson->id, $completedLessonIds);
                                @endphp
                                <li>
                                    <a href="{{ route('student.player.show', $course) }}?lesson={{ $lesson->id }}"
                                       class="flex items-start gap-2.5 px-3 py-2.5 rounded-xl text-xs transition
                                              {{ $isActive
                                                  ? 'bg-brand-500 text-white font-semibold shadow-soft'
                                                  : 'text-ink-600 hover:bg-cream-2' }}">
                                        <span class="mt-0.5 flex-shrink-0 w-4 h-4 grid place-items-center">
                                            @if ($isCompleted)
                                                <i class="fa-solid fa-circle-check {{ $isActive ? 'text-white' : 'text-brand-500' }}"></i>
                                            @elseif ($isActive)
                                                <i class="fa-solid fa-play text-[10px] text-white"></i>
                                            @else
                                                <i class="fa-regular fa-circle text-ink-300"></i>
                                            @endif
                                        </span>
                                        <span class="line-clamp-2 leading-snug">{{ $lesson->title }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-xs text-ink-400 italic">Sin lecciones</li>
                            @endforelse
                        </ul>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <i class="fa-regular fa-folder-open text-2xl text-ink-300"></i>
                        <p class="text-xs text-ink-400 mt-2 italic">Este curso aún no tiene contenido.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </aside>

    {{-- ════════ Zona principal ════════ --}}
    <main class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-cream/90 backdrop-blur-md border-b border-ink-200/70 px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" @click="sidebarOpen = !sidebarOpen"
                        class="grid place-items-center w-10 h-10 rounded-2xl bg-white border border-ink-200/70 hover:bg-cream-2 transition flex-shrink-0" aria-label="Menú">
                    <i class="fa-solid fa-bars text-ink-700"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm md:text-base font-display font-bold text-ink-900 truncate">{{ $course->title }}</h1>
                    @if ($currentLesson)
                        <p class="text-xs text-ink-500 truncate">{{ $currentLesson->title }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('student.enrolled-courses.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl bg-white border border-ink-200/70 hover:bg-cream-2 text-ink-700 text-xs font-semibold transition flex-shrink-0">
                <i class="fa-solid fa-arrow-left"></i> Mis cursos
            </a>
        </header>

        <div class="flex-1 p-4 md:p-6 lg:p-8 max-w-4xl w-full mx-auto space-y-5">

            {{-- ── Visor de contenido ── --}}
            <div class="w-full rounded-3xl overflow-hidden border border-ink-200/70 bg-ink-950 shadow-lift">
                @if ($currentLesson)
                    @php
                        $storagePath = $currentLesson->file_path ?? '';
                        $mime        = $currentLesson->file_type ?? 'video/mp4';
                        $youtubeId   = null; $vimeoId = null;
                        if ($currentLesson->storage === 'youtube') {
                            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $storagePath, $ym);
                            $youtubeId = $ym[1] ?? null;
                        } elseif ($currentLesson->storage === 'vimeo') {
                            preg_match('/vimeo\.com\/(\d+)/', $storagePath, $vm);
                            $vimeoId = $vm[1] ?? null;
                        }
                    @endphp

                    @switch($currentLesson->storage)
                        @case('youtube')
                            <div class="aspect-video">
                                @if ($youtubeId)
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0&modestbranding=1"
                                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                @else
                                    <div class="w-full h-full grid place-items-center text-ink-300 text-sm">URL de YouTube no válida.</div>
                                @endif
                            </div>
                            @break

                        @case('vimeo')
                            <div class="aspect-video">
                                @if ($vimeoId)
                                    <iframe class="w-full h-full" src="https://player.vimeo.com/video/{{ $vimeoId }}?title=0&byline=0&portrait=0"
                                            frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                @else
                                    <div class="w-full h-full grid place-items-center text-ink-300 text-sm">URL de Vimeo no válida.</div>
                                @endif
                            </div>
                            @break

                        @case('upload')
                            <div class="aspect-video">
                                @if (str_starts_with($mime, 'video/'))
                                    <video class="w-full h-full" controls>
                                        <source src="{{ \Illuminate\Support\Facades\Storage::url($storagePath) }}" type="{{ $mime }}">
                                    </video>
                                @elseif (str_starts_with($mime, 'audio/'))
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-6 p-8">
                                        <i class="fa-solid fa-music text-5xl text-brand-400"></i>
                                        <p class="text-cream font-semibold">{{ $currentLesson->title }}</p>
                                        <audio class="w-full max-w-lg" controls>
                                            <source src="{{ \Illuminate\Support\Facades\Storage::url($storagePath) }}" type="{{ $mime }}">
                                        </audio>
                                    </div>
                                @elseif ($mime === 'application/pdf')
                                    <iframe class="w-full h-full" src="{{ \Illuminate\Support\Facades\Storage::url($storagePath) }}#toolbar=1" frameborder="0"></iframe>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-5">
                                        <i class="fa-solid fa-file-lines text-5xl text-ink-400"></i>
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($storagePath) }}" download
                                           class="px-5 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition">Descargar archivo</a>
                                    </div>
                                @endif
                            </div>
                            @break

                        @case('url')
                            <div class="aspect-video flex flex-col items-center justify-center gap-4">
                                <i class="fa-solid fa-link text-4xl text-ink-400"></i>
                                <a href="{{ $storagePath }}" target="_blank" rel="noopener noreferrer"
                                   class="px-5 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition">Abrir recurso externo →</a>
                            </div>
                            @break

                        @default
                            {{-- Lección de solo texto / sin archivo --}}
                            <div class="aspect-video flex flex-col items-center justify-center gap-3 bg-gradient-to-br from-brand-500 to-brand-700 text-white text-center px-8">
                                <i class="fa-solid fa-book-open text-4xl opacity-90"></i>
                                <p class="font-display font-bold text-lg">{{ $currentLesson->title }}</p>
                                <p class="text-sm text-white/80">Lee el contenido de la lección abajo 👇</p>
                            </div>
                    @endswitch
                @else
                    <div class="aspect-video grid place-items-center text-ink-300 text-sm">Selecciona una lección del menú lateral.</div>
                @endif
            </div>

            {{-- Flash --}}
            @if (session('error'))
                <div class="px-4 py-3 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 text-sm">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
                </div>
            @endif

            {{-- ── Barra de acción: marcar completada + descarga ── --}}
            @if ($currentLesson)
                <div class="flex flex-wrap items-center gap-3" id="lesson-actions"
                     x-data="completionBtn({{ $currentLesson->id }}, {{ $isCurrentLessonCompleted ? 'true' : 'false' }}, {{ $progress }})">
                    <button type="button" @click="toggle()" :disabled="loading"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-semibold transition border"
                            :class="completed
                                ? 'bg-brand-50 border-brand-200 text-brand-700 hover:bg-brand-100'
                                : 'bg-brand-500 border-brand-500 text-white hover:bg-brand-600 shadow-soft'">
                        <i class="fa-solid" :class="completed ? 'fa-circle-check' : 'fa-circle'"></i>
                        <span x-text="completed ? '¡Lección completada!' : 'Marcar como completada'"></span>
                    </button>

                    @if ($currentLesson->downloadable && $currentLesson->file_path)
                        <a href="{{ $currentLesson->storage === 'upload' ? \Illuminate\Support\Facades\Storage::url($currentLesson->file_path) : $currentLesson->file_path }}"
                           download
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-ink-200/70 hover:bg-cream-2 text-ink-700 text-sm font-medium transition">
                            <i class="fa-solid fa-download"></i> Material
                        </a>
                    @endif

                    <span class="ml-auto text-xs text-ink-500" x-text="'Progreso del curso: ' + progress + '%'"></span>
                </div>
            @endif

            {{-- ── Info / contenido de la lección ── --}}
            @if ($currentLesson && $currentLesson->description)
                <div class="bg-white border border-ink-200/70 rounded-3xl p-5 md:p-6 shadow-soft">
                    <h3 class="font-display font-bold text-lg text-ink-900 mb-2">{{ $currentLesson->title }}</h3>
                    <div class="article-prose text-ink-700 text-sm leading-relaxed">
                        {!! \Mews\Purifier\Facades\Purifier::clean($currentLesson->description) !!}
                    </div>
                </div>
            @endif

            {{-- ════════ QUIZ · autoevaluación (FREE mínimo) ════════ --}}
            @if ($quiz && $quiz->questions->isNotEmpty())
                @php
                    $retry       = request()->boolean('retry');
                    $showResults = $lastAttempt && ! $retry;
                    $attempts    = $quiz->attempts()->where('user_id', auth()->id())->whereNotNull('completed_at')->count();
                    $answersByQ  = $showResults ? $lastAttempt->answers->keyBy('question_id') : collect();
                @endphp

                <div id="quiz" class="bg-white border-2 border-brand-200 rounded-3xl p-5 md:p-7 shadow-soft scroll-mt-24">
                    <div class="flex items-start gap-3 mb-1">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 shrink-0">
                            <i class="fa-solid fa-circle-question"></i>
                        </span>
                        <div>
                            <h3 class="font-display font-bold text-lg text-ink-900">{{ $quiz->title }}</h3>
                            @if ($quiz->description)
                                <p class="text-sm text-ink-500">{{ $quiz->description }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($showResults)
                        {{-- ── RESULTADO ── --}}
                        @php $passed = $lastAttempt->passed; @endphp
                        <div class="mt-4 mb-6 p-5 rounded-2xl text-center {{ $passed ? 'bg-brand-50 border border-brand-200' : 'bg-sun-50 border border-sun-200' }}">
                            <div class="text-4xl mb-1">{{ $passed ? '🎉' : '💪' }}</div>
                            <p class="font-display font-extrabold text-2xl {{ $passed ? 'text-brand-700' : 'text-ink-800' }}">
                                {{ (int) $lastAttempt->percentage }}%
                            </p>
                            <p class="text-sm text-ink-600 mt-1">
                                {{ $passed
                                    ? '¡Bien hecho! Has superado la autoevaluación.'
                                    : 'Casi lo tienes. Repasa la lección e inténtalo de nuevo.' }}
                            </p>
                        </div>

                        <div class="space-y-4">
                            @foreach ($quiz->questions as $i => $question)
                                @php
                                    $ans       = $answersByQ->get($question->id);
                                    $chosenId  = $ans ? (int) $ans->answer : null;
                                    $isCorrect = $ans?->is_correct;
                                @endphp
                                <div class="rounded-2xl border {{ $isCorrect === true ? 'border-brand-200 bg-brand-50/40' : ($isCorrect === false ? 'border-coral-200 bg-coral-50/40' : 'border-ink-200/70') }} p-4">
                                    <p class="font-semibold text-ink-900 text-sm flex gap-2">
                                        <span class="text-ink-400">{{ $i + 1 }}.</span>
                                        <span>{{ $question->question }}</span>
                                        @if ($isCorrect === true)
                                            <i class="fa-solid fa-circle-check text-brand-500 ml-auto"></i>
                                        @elseif ($isCorrect === false)
                                            <i class="fa-solid fa-circle-xmark text-coral-500 ml-auto"></i>
                                        @endif
                                    </p>
                                    <ul class="mt-3 space-y-1.5">
                                        @foreach ($question->options as $opt)
                                            @php
                                                $isChosen  = $chosenId === (int) $opt->id;
                                                $isRight   = $opt->is_correct;
                                            @endphp
                                            <li class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm
                                                {{ $isRight ? 'bg-brand-100 text-brand-800 font-medium'
                                                   : ($isChosen ? 'bg-coral-100 text-coral-800' : 'bg-cream-2 text-ink-600') }}">
                                                <i class="fa-solid {{ $isRight ? 'fa-check text-brand-600' : ($isChosen ? 'fa-xmark text-coral-600' : 'fa-minus text-ink-300') }} w-4"></i>
                                                <span>{{ $opt->option_text }}</span>
                                                @if ($isChosen && ! $isRight)
                                                    <span class="ml-auto text-[11px] text-coral-600">tu respuesta</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($question->explanation)
                                        <p class="mt-3 text-xs text-ink-600 bg-cream-2 rounded-xl px-3 py-2">
                                            <i class="fa-solid fa-lightbulb text-sun-500 mr-1"></i> {{ $question->explanation }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($quiz->allow_retakes && ($quiz->max_attempts === 0 || $attempts < $quiz->max_attempts))
                            <a href="{{ route('student.player.show', $course) }}?lesson={{ $currentLesson->id }}&retry=1#quiz"
                               class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition shadow-soft">
                                <i class="fa-solid fa-rotate-right"></i> Reintentar
                            </a>
                        @else
                            <p class="mt-6 text-xs text-ink-400">Has usado todos tus intentos en esta autoevaluación.</p>
                        @endif

                    @else
                        {{-- ── FORMULARIO ── --}}
                        <form action="{{ route('student.quiz.submit', $quiz) }}" method="POST" class="mt-5 space-y-5">
                            @csrf
                            @foreach ($quiz->questions as $i => $question)
                                <fieldset class="rounded-2xl border border-ink-200/70 p-4">
                                    <legend class="px-2 text-sm font-semibold text-ink-900">
                                        {{ $i + 1 }}. {{ $question->question }}
                                    </legend>
                                    <div class="mt-2 space-y-2">
                                        @foreach ($question->options as $opt)
                                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-cream-2 hover:bg-brand-50 cursor-pointer transition border border-transparent has-[:checked]:border-brand-300 has-[:checked]:bg-brand-50">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt->id }}"
                                                       {{ $question->required ? 'required' : '' }}
                                                       class="w-4 h-4 text-brand-500 focus:ring-brand-400">
                                                <span class="text-sm text-ink-700">{{ $opt->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach

                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold transition shadow-soft">
                                <i class="fa-solid fa-paper-plane"></i> Enviar respuestas
                            </button>
                            <p class="text-xs text-ink-400">Autoevaluación · no afecta a ningún certificado. Es solo para que compruebes lo aprendido.</p>
                        </form>
                    @endif
                </div>
            @endif

            {{-- ── Navegación anterior / siguiente ── --}}
            <div class="flex items-center justify-between gap-4 pt-2">
                @if ($prevLesson)
                    <a href="{{ route('student.player.show', $course) }}?lesson={{ $prevLesson->id }}"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-ink-200/70 hover:bg-cream-2 text-ink-700 text-sm font-medium transition min-w-0 max-w-xs">
                        <i class="fa-solid fa-arrow-left flex-shrink-0"></i>
                        <span class="truncate">{{ $prevLesson->title }}</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if ($nextLesson)
                    <a href="{{ route('student.player.show', $course) }}?lesson={{ $nextLesson->id }}"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition min-w-0 max-w-xs shadow-soft">
                        <span class="truncate">{{ $nextLesson->title }}</span>
                        <i class="fa-solid fa-arrow-right flex-shrink-0"></i>
                    </a>
                @else
                    <div></div>
                @endif
            </div>

        </div>
    </main>
</div>

<script>
function completionBtn(lessonId, initialCompleted, initialProgress) {
    return {
        completed: initialCompleted,
        loading: false,
        progress: initialProgress,
        async toggle() {
            this.loading = true;
            try {
                const url = '{{ route('student.player.lesson.toggle-complete', ['course' => ':course', 'lesson' => ':lesson']) }}'
                    .replace(':course', '{{ $course->slug }}')
                    .replace(':lesson', lessonId);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.completed = data.completed;
                this.progress  = data.progress;
                // Actualizar barra del sidebar en vivo
                const bar = document.getElementById('sidebar-progress-bar');
                const pct = document.getElementById('sidebar-progress-pct');
                if (bar) bar.style.width = data.progress + '%';
                if (pct) pct.textContent = data.progress + '%';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>

</body>
</html>
