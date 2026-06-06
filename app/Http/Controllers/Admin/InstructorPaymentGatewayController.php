<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstructorPaymentGatewayRequest;
use App\Models\InstructorPaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructorPaymentGatewayController extends Controller
{
    public function index(): View
    {
        $gateways = InstructorPaymentGateway::latest()->paginate(15);

        return view('admin.instructor-payment-gateways.index', compact('gateways'));
    }

    public function create(): View
    {
        return view('admin.instructor-payment-gateways.create');
    }

    public function store(InstructorPaymentGatewayRequest $request): RedirectResponse
    {
        InstructorPaymentGateway::create([
            'name'         => $request->name,
            'type'         => $request->type,
            'instructions' => $request->instructions,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        notyf()->success('Pasarela de pago creada correctamente.');

        return redirect()->route('admin.instructor-payment-gateways.index');
    }

    public function edit(InstructorPaymentGateway $instructorPaymentGateway): View
    {
        return view('admin.instructor-payment-gateways.edit', [
            'gateway' => $instructorPaymentGateway,
        ]);
    }

    public function update(InstructorPaymentGatewayRequest $request, InstructorPaymentGateway $instructorPaymentGateway): RedirectResponse
    {
        $instructorPaymentGateway->update([
            'name'         => $request->name,
            'type'         => $request->type,
            'instructions' => $request->instructions,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        notyf()->success('Pasarela de pago actualizada correctamente.');

        return redirect()->route('admin.instructor-payment-gateways.index');
    }

    public function destroy(InstructorPaymentGateway $instructorPaymentGateway): \Illuminate\Http\JsonResponse
    {
        // Protección: no eliminar si hay instructores usando esta pasarela
        // (cuando exista la tabla instructor_payout_information en L5)
        $instructorPaymentGateway->delete();

        return response()->json(['message' => 'Pasarela eliminada correctamente.']);
    }
}
