<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /** Roles que puede crear el admin → etiqueta. */
    public const CREATABLE_ROLES = [
        'student' => 'Estudiante',
        'instructor' => 'Instructor',
        'superadmin' => 'Superadmin',
    ];

    public function index(Request $request): View
    {
        $query = User::query()
            ->withCount(['enrollments', 'orders', 'courses'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        // El rol decide la tabla: superadmin → admins; el resto → users.
        $role = $request->input('role');
        $table = $role === 'superadmin' ? 'admins' : 'users';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', Rule::in(array_keys(self::CREATABLE_ROLES))],
            'email' => ['required', 'email', 'max:160', Rule::unique($table, 'email')],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($data['role'] === 'superadmin') {
            $admin = new Admin;
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->password = $data['password']; // cast 'hashed'
            $admin->save();

            return redirect()->route('admin.admins.index')
                ->with('success', "Superadmin «{$admin->name}» creado.");
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password']; // cast 'hashed'
        $user->save();

        // role/approve_status/is_active NO son asignables en masa: se fijan aquí.
        // Al crearlo el admin, el instructor queda aprobado directamente.
        $user->forceFill([
            'role' => $data['role'],
            'approve_status' => 'approved',
            'is_active' => true,
        ])->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario «{$user->name}» creado.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['student', 'instructor'])],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password']; // cast 'hashed'
        }
        $user->save();

        // role no es asignable en masa: se concede explícitamente.
        $user->forceFill(['role' => $data['role']])->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario «{$user->name}» actualizado.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete(); // soft delete (el modelo usa SoftDeletes)

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario «{$name}» eliminado.");
    }

    public function show(User $user): View
    {
        $user->loadCount(['enrollments', 'orders', 'courses']);
        $user->load([
            'enrollments' => fn ($q) => $q->with('course')->latest()->limit(5),
            'orders' => fn ($q) => $q->latest()->limit(5),
            'courses' => fn ($q) => $q->latest()->limit(5),
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $user->forceFill(['is_active' => ! $user->is_active])->save();

        $label = $user->is_active ? 'activado' : 'desactivado';

        if ($request->wantsJson()) {
            return response()->json([
                'is_active' => $user->is_active,
                'message' => "Usuario {$label} correctamente.",
            ]);
        }

        return back()->with('success', "Usuario {$label} correctamente.");
    }
}
