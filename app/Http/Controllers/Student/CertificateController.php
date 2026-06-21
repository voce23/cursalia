<?php

namespace App\Http\Controllers\Student;

use App\Helpers\Pro;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\GeneralSetting;
use App\Models\LessonCompletion;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Certificado de finalización (complemento PRO). Se genera como página HTML
 * lista para imprimir / guardar como PDF (sin librerías ni fuentes externas
 * en el servidor). Solo si: la academia tiene PRO, el alumno está inscrito,
 * el curso emite certificado y completó el 100% de las lecciones.
 */
class CertificateController extends Controller
{
    public function show(Course $course): View
    {
        // El complemento Certificados es PRO (una llave PRO lo desbloquea).
        abort_unless(Pro::isActive(), 404);

        $user = Auth::user();

        abort_unless(
            $user->enrollments()->where('course_id', $course->id)->where('have_access', true)->exists(),
            403
        );
        abort_unless($course->certificate, 404);

        $totalLessons = $course->lessons()->count();
        abort_if($totalLessons === 0, 404);

        $completed = LessonCompletion::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count();

        abort_unless($completed >= $totalLessons, 403);

        return view('student.certificates.show', [
            'studentName' => $user->name,
            'courseTitle' => $course->title,
            'date' => now()->translatedFormat('d \d\e F \d\e Y'),
            'academy' => optional(GeneralSetting::first())->site_name ?: config('app.name'),
            'instructor' => $course->instructor?->name,
            'code' => strtoupper(substr(sha1($user->id.'-'.$course->id.'-cursalia'), 0, 10)),
        ]);
    }
}
