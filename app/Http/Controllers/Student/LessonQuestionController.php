<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseChapterLesson;
use App\Models\LessonQuestion;
use App\Models\LessonQuestionAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonQuestionController extends Controller
{
    public function store(Request $request, CourseChapterLesson $lesson): RedirectResponse
    {
        $this->authorizeEnrolled($lesson->course_id);

        $request->validate(['body' => ['required', 'string', 'min:5', 'max:2000']]);

        LessonQuestion::create([
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->course_id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        flash()->success('Pregunta publicada.');

        return back()->withFragment('qna');
    }

    public function storeAnswer(Request $request, LessonQuestion $question): RedirectResponse
    {
        $this->authorizeEnrolledOrInstructor($question);

        $request->validate(['body' => ['required', 'string', 'min:2', 'max:4000']]);

        LessonQuestionAnswer::create([
            'question_id' => $question->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        flash()->success('Respuesta publicada.');

        return back()->withFragment('qna');
    }

    public function destroyQuestion(LessonQuestion $question): RedirectResponse
    {
        abort_unless(Auth::id() === $question->user_id, 403);

        $question->delete();

        flash()->success('Pregunta eliminada.');

        return back()->withFragment('qna');
    }

    public function destroyAnswer(LessonQuestionAnswer $answer): RedirectResponse
    {
        $question = $answer->question;
        abort_unless(
            Auth::id() === $answer->user_id || Auth::id() === $question->lesson->instructor_id,
            403
        );

        $answer->delete();

        flash()->success('Respuesta eliminada.');

        return back()->withFragment('qna');
    }

    private function authorizeEnrolled(int $courseId): void
    {
        $enrolled = Auth::user()->enrollments()
            ->where('course_id', $courseId)
            ->where('have_access', true)
            ->exists();

        abort_unless($enrolled, 403);
    }

    private function authorizeEnrolledOrInstructor(LessonQuestion $question): void
    {
        $user = Auth::user();

        if ($user->role === 'instructor' && $user->id === $question->lesson->instructor_id) {
            return;
        }

        $enrolled = $user->enrollments()
            ->where('course_id', $question->course_id)
            ->where('have_access', true)
            ->exists();

        abort_unless($enrolled, 403);
    }
}
