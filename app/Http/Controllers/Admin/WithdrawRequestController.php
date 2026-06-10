<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Notifications\WithdrawStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WithdrawRequestController extends Controller
{
    public function index(): View
    {
        $withdrawRequests = Withdraw::query()
            ->with(['user:id,name,email', 'gateway:id,name,type'])
            ->latest()
            ->paginate(15);

        return view('admin.withdraws.index', compact('withdrawRequests'));
    }

    public function update(Request $request, Withdraw $withdraw): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:1000', 'required_if:status,rejected'],
        ]);

        if ($withdraw->status !== 'pending') {
            notyf()->error('Solo se pueden procesar solicitudes pendientes.');

            return back();
        }

        $withdraw->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'processed_at' => now(),
        ]);

        Log::info('admin.withdraw_request_updated', [
            'admin_id' => auth('admin')->id(),
            'withdraw_id' => $withdraw->id,
            'user_id' => $withdraw->user_id,
            'amount' => $withdraw->amount,
            'new_status' => $request->status,
            'ip' => $request->ip(),
        ]);

        // Notificar al instructor
        $withdraw->user->notify(new WithdrawStatusNotification($withdraw));

        notyf()->success('Solicitud de retiro actualizada correctamente.');

        return back();
    }
}
