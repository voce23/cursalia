<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\WithdrawStoreRequest;
use App\Models\Admin;
use App\Models\OrderItem;
use App\Models\Withdraw;
use App\Notifications\NewWithdrawRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WithdrawController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Withdraw::class);

        $user = Auth::user();

        $withdrawals = Withdraw::query()
            ->where('user_id', $user->id)
            ->with('gateway:id,name,type')
            ->latest()
            ->paginate(10);

        $totalEarnings = OrderItem::query()
            ->whereHas('course', fn ($query) => $query->where('instructor_id', $user->id))
            ->sum('instructor_earning');

        $totalApproved = Withdraw::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $totalPending = Withdraw::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, (float) $totalEarnings - (float) $totalApproved);
        $availableToRequest = max(0, $availableBalance - (float) $totalPending);

        $payoutInfo = $user->payoutInformation;

        return view('instructor.withdraws.index', compact(
            'withdrawals',
            'totalEarnings',
            'totalApproved',
            'totalPending',
            'availableBalance',
            'availableToRequest',
            'payoutInfo'
        ));
    }

    public function store(WithdrawStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Withdraw::class);

        $user = Auth::user();

        $payoutInfo = $user->payoutInformation;

        if (! $payoutInfo || ! $payoutInfo->gateway_id) {
            flash()->error('Primero configura tu información de pago para solicitar retiros.');

            return redirect()->route('instructor.payout-information.index');
        }

        // Cálculo de saldo + creación dentro de una transacción con bloqueo
        // para serializar solicitudes concurrentes y evitar doble retiro (TOCTOU).
        try {
            $withdraw = DB::transaction(function () use ($request, $user, $payoutInfo) {
                Withdraw::where('user_id', $user->id)->lockForUpdate()->get();

                $totalEarnings = OrderItem::query()
                    ->whereHas('course', fn ($query) => $query->where('instructor_id', $user->id))
                    ->sum('instructor_earning');

                $totalApproved = Withdraw::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount');

                $totalPending = Withdraw::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->sum('amount');

                $availableToRequest = max(0, ((float) $totalEarnings - (float) $totalApproved) - (float) $totalPending);

                if ((float) $request->amount > $availableToRequest) {
                    throw ValidationException::withMessages([
                        'amount' => 'El monto supera tu saldo disponible para solicitar (considerando retiros pendientes).',
                    ]);
                }

                return Withdraw::create([
                    'user_id' => $user->id,
                    'gateway_id' => $payoutInfo->gateway_id,
                    'amount' => $request->amount,
                    'status' => 'pending',
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Notificar a todos los admins
        $withdraw->load('user');
        Admin::all()->each(fn ($admin) => $admin->notify(new NewWithdrawRequestNotification($withdraw)));

        flash()->success('Solicitud de retiro enviada correctamente.');

        return redirect()->route('instructor.withdraws.index');
    }
}
