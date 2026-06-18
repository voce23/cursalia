<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseOrder;
use App\Models\Enrollment;

/**
 * Ventas de cursos — el dueño aprueba o rechaza los pagos manuales (QR / transferencia).
 * Al aprobar, se inscribe al alumno en el curso.
 */
class CourseOrderController extends Controller
{
    public function index()
    {
        $orders = CourseOrder::with(['user:id,name,email', 'course:id,title,slug'])
            ->orderByRaw("FIELD(status,'pending','approved','rejected')")
            ->latest()
            ->paginate(20);

        return view('admin.course-orders.index', compact('orders'));
    }

    public function approve(CourseOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        // withTrashed: recupera la inscripción si fue retirada antes (borrado suave),
        // en vez de chocar con el índice único.
        $enrollment = Enrollment::withTrashed()->firstOrNew([
            'user_id' => $order->user_id,
            'course_id' => $order->course_id,
        ]);
        $enrollment->instructor_id = $order->instructor_id;
        $enrollment->have_access = true;
        if ($enrollment->trashed()) {
            $enrollment->restore();
        }
        $enrollment->save();

        $order->update(['status' => 'approved']);
        flash()->success('Pago aprobado. El alumno ya tiene acceso al curso.');

        return back();
    }

    public function reject(CourseOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Este pedido ya fue procesado.');
        }

        $order->update(['status' => 'rejected']);
        flash()->success('Pago rechazado.');

        return back();
    }
}
