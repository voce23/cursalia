<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
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

    public function show(User $user): View
    {
        $user->loadCount(['enrollments', 'orders', 'courses']);
        $user->load([
            'enrollments' => fn ($q) => $q->with('course')->latest()->limit(5),
            'orders'      => fn ($q) => $q->latest()->limit(5),
            'courses'     => fn ($q) => $q->latest()->limit(5),
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $user->forceFill(['is_active' => ! $user->is_active])->save();

        $label = $user->is_active ? 'activado' : 'desactivado';

        flash()->success("Usuario {$label} correctamente.");

        return response()->json([
            'is_active' => $user->is_active,
            'message'   => "Usuario {$label} correctamente.",
        ]);
    }
}
