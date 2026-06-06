<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordUpdateRequest;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use App\Models\Admin;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        return view('admin.profile.index', [
            'admin' => $admin,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $data = $request->only(
            'name', 'email', 'bio',
            'headline', 'social_x', 'social_linkedin', 'social_github', 'social_youtube', 'social_web',
        );

        if ($request->hasFile('image')) {
            if ($admin->image) {
                Storage::disk('public')->delete($admin->image);
            }

            // Avatar: cuadrado 200×200, sin responsive (no necesita tantos tamaños).
            $data['image'] = app(ImageOptimizer::class)->processUpload(
                file: $request->file('image'),
                folder: 'avatars',
                targetWidth: 200,
                targetHeight: 200,
                cover: true,
            );
        }

        $admin->update($data);

        return back()->with('profile_success', 'Perfil actualizado correctamente.')->with('active_tab', 'info');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $admin->update([
            'password' => $request->password,
        ]);

        return back()->with('password_success', 'Contraseña actualizada correctamente.')->with('active_tab', 'password');
    }
}
