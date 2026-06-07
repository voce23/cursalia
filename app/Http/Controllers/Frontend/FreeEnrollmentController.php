<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Inscripción gratuita de un alumno a un curso (Cursalia FREE / FASE 1).
 *
 * - Solo procesa cursos con price = 0 (los de pago están reservados a FASE 2,
 *   donde entrará el carrito + Stripe/PayPal).
 * - Idempotente: si el alumno ya está inscrito, no duplica; solo lo redirige.
 */
class FreeEnrollmentController extends Controller
{
    public function store(Course $course): RedirectResponse
    {
        // Solo cursos aprobados, activos y gratuitos
        abort_unless(
            $course->is_approved === 'approved'
                && $course->status === 'active'
                && (float) $course->price === 0.0,
            403,
            'Este curso no se puede inscribir gratuitamente.'
        );

        $user = Auth::user();

        // No permitir que un instructor se inscriba a su propio curso
        if ($user->id === $course->instructor_id) {
            return back()->with('status', 'No puedes inscribirte a tu propio curso.');
        }

        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['instructor_id' => $course->instructor_id, 'have_access' => true]
        );

        return redirect()
            ->route('student.player.show', $course)
            ->with('status', '¡Listo! Ya estás inscrito en "'.$course->title.'". Empieza cuando quieras.');
    }
}
