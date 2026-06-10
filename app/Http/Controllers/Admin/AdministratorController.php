<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdministratorController extends Controller
{
    public function index(): View
    {
        $admins = Admin::orderBy('name')->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.form', ['admin' => new Admin]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $admin = new Admin;
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->password = $data['password']; // el cast 'hashed' lo cifra solo
        $admin->save();

        return redirect()->route('admin.admins.index')
            ->with('success', "Administrador «{$admin->name}» creado.");
    }

    public function edit(Admin $admin): View
    {
        return view('admin.admins.form', compact('admin'));
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $data = $this->validated($request, $admin);

        $admin->name = $data['name'];
        $admin->email = $data['email'];

        if (! empty($data['password'])) {
            $admin->password = $data['password']; // cast 'hashed'
        }
        $admin->save();

        return redirect()->route('admin.admins.index')
            ->with('success', "Administrador «{$admin->name}» actualizado.");
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if (Admin::count() <= 1) {
            return back()->with('error', 'No puedes eliminar al último administrador.');
        }

        $name = $admin->name;
        $admin->delete();

        return back()->with('success', "Administrador «{$name}» eliminado.");
    }

    /**
     * Contraseña obligatoria al crear, opcional al editar (vacía = mantener).
     * Fuerza: mínimo 8, mayúsculas y minúsculas, y números (estilo Google).
     */
    private function validated(Request $request, ?Admin $admin = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('admins', 'email')->ignore($admin?->id)],
            'password' => [
                $admin === null ? 'required' : 'nullable',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ]);
    }
}
