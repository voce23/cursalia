<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseChapterLesson;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Admin · Quiz builder mínimo (Cursalia FREE).
 *
 * El dueño de la academia puede adjuntar UNA autoevaluación por lección, con
 * preguntas de opción múltiple / verdadero-falso. Sin certificados (eso es PRO).
 */
class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $lessons = CourseChapterLesson::query()
            ->with(['course:id,title', 'chapter:id,title'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Mapa lesson_id → quiz (existencia + id) en una sola query.
        $quizzes = Quiz::query()
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get(['id', 'lesson_id', 'title'])
            ->keyBy('lesson_id');

        return view('admin.quizzes.index', compact('lessons', 'quizzes', 'search'));
    }

    public function create(Request $request): View
    {
        $lessonId = $request->integer('lesson');
        $lesson = CourseChapterLesson::with('course:id,title')->findOrFail($lessonId);

        // Si ya existe quiz para esta lección, ir a editar.
        $existing = Quiz::where('lesson_id', $lesson->id)->first();
        if ($existing) {
            return $this->edit($existing);
        }

        $quiz = new Quiz([
            'title' => 'Autoevaluación: '.$lesson->title,
            'passing_score' => 70,
            'allow_retakes' => true,
            'max_attempts' => 3,
            'status' => true,
        ]);
        $quiz->setRelation('questions', collect());

        return view('admin.quizzes.form', compact('quiz', 'lesson'));
    }

    public function edit(Quiz $quiz): View
    {
        $quiz->load([
            'questions' => fn ($q) => $q->orderBy('order'),
            'questions.options' => fn ($q) => $q->orderBy('order'),
        ]);
        $lesson = CourseChapterLesson::with('course:id,title')->findOrFail($quiz->lesson_id);

        return view('admin.quizzes.form', compact('quiz', 'lesson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $lesson = CourseChapterLesson::findOrFail($request->integer('lesson_id'));

        DB::transaction(function () use ($data, $lesson) {
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'passing_score' => $data['passing_score'],
                'allow_retakes' => $data['allow_retakes'] ?? false,
                'max_attempts' => $data['max_attempts'],
                'status' => $data['status'] ?? false,
            ]);

            $this->syncQuestions($quiz, $data['questions']);
        });

        return redirect()->route('admin.quizzes.index')->with('success', 'Autoevaluación creada.');
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data, $quiz) {
            $quiz->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'passing_score' => $data['passing_score'],
                'allow_retakes' => $data['allow_retakes'] ?? false,
                'max_attempts' => $data['max_attempts'],
                'status' => $data['status'] ?? false,
            ]);

            // Reemplazar preguntas (simple y robusto para el builder mínimo).
            $quiz->questions()->delete();
            $this->syncQuestions($quiz, $data['questions']);
        });

        return redirect()->route('admin.quizzes.index')->with('success', 'Autoevaluación actualizada.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Autoevaluación eliminada.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'lesson_id' => ['required', 'exists:course_chapter_lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'allow_retakes' => ['nullable', 'boolean'],
            'max_attempts' => ['required', 'integer', 'min:0', 'max:50'],
            'status' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string', 'max:1000'],
            'questions.*.type' => ['required', 'in:multiple_choice,true_false'],
            'questions.*.explanation' => ['nullable', 'string', 'max:1000'],
            'questions.*.options' => ['required', 'array', 'min:2'],
            'questions.*.options.*.text' => ['required', 'string', 'max:500'],
            'questions.*.correct' => ['required'],
        ]);
    }

    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        foreach (array_values($questions) as $qi => $q) {
            $question = $quiz->questions()->create([
                'question' => $q['question'],
                'question_type' => $q['type'],
                'order' => $qi,
                'required' => true,
                'points' => 1,
                'explanation' => $q['explanation'] ?? null,
            ]);

            $correctIndex = (int) ($q['correct'] ?? 0);
            foreach (array_values($q['options']) as $oi => $opt) {
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct' => $oi === $correctIndex,
                    'order' => $oi,
                ]);
            }
        }
    }
}
