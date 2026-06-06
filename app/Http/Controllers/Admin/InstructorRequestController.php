<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InstructorRequestApprovedMail;
use App\Mail\InstructorRequestRejectMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InstructorRequestController extends Controller
{
    public function index(): View
    {
        $instructorRequests = User::query()
            ->where('role', 'instructor')
            ->whereIn('approve_status', ['pending', 'rejected'])
            ->latest()
            ->paginate(10);

        return view('admin.instructor-request.index', compact('instructorRequests'));
    }

    public function update(Request $request, User $instructor_request): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $instructor_request->approve_status = $request->status;

        if ($request->status === 'approved') {
            $instructor_request->role = 'instructor';
        }

        $instructor_request->save();

        Log::info('admin.instructor_request_updated', [
            'admin_id'       => auth('admin')->id(),
            'instructor_id'  => $instructor_request->id,
            'instructor_email' => $instructor_request->email,
            'new_status'     => $request->status,
            'ip'             => $request->ip(),
        ]);

        if ($request->status === 'approved') {
            Mail::to($instructor_request)->queue(new InstructorRequestApprovedMail());
        } elseif ($request->status === 'rejected') {
            Mail::to($instructor_request)->queue(new InstructorRequestRejectMail());
        }

        flash()->success('Estado actualizado correctamente.');

        return redirect()->route('admin.instructor-requests.index');
    }

    public function download(User $user)
    {
        abort_if($user->role !== 'instructor', 403);
        abort_if(
            ! $user->document || str_contains($user->document, '..'),
            404
        );

        if (Storage::disk('public')->exists($user->document)) {
            return response()->download(Storage::disk('public')->path($user->document));
        }

        flash()->error('El documento no existe.');

        return back();
    }
}
