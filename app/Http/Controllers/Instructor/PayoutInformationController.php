<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\PayoutInformationRequest;
use App\Models\InstructorPaymentGateway;
use App\Models\InstructorPayoutInformation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayoutInformationController extends Controller
{
    /**
     * Mostrar el formulario de información de pago del instructor.
     */
    public function index(): View
    {
        // Gateways activas para el select
        $gateways = InstructorPaymentGateway::where('is_active', true)->get();

        // Información de pago actual (o null si no existe)
        $info = Auth::user()->payoutInformation;

        return view('instructor.payout-information.index', compact('gateways', 'info'));
    }

    /**
     * Guardar o actualizar la información de pago.
     */
    public function update(PayoutInformationRequest $request): RedirectResponse
    {
        InstructorPayoutInformation::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->validated() + ['user_id' => Auth::id()],
        );

        notyf()->success('Información de pago actualizada correctamente.');

        return redirect()->route('instructor.payout-information.index');
    }
}
