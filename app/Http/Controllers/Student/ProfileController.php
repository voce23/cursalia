<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PasswordUpdateRequest;
use App\Http\Requests\Student\ProfileUpdateRequest;
use App\Http\Requests\Student\SocialUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('student.profile.index', [
            'user' => $user,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->only('name', 'email', 'headline', 'phone', 'gender', 'bio');

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $filename = 'avatars/' . uniqid('avatar_') . '.webp';

            Image::decode($request->file('image'))
                ->cover(200, 200)
                ->save(Storage::disk('public')->path($filename), 90);

            $data['image'] = $filename;
        }

        $user->update($data);

        return back()->with('profile_success', 'Perfil actualizado correctamente.')->with('active_tab', 'info');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'password' => $request->password,
        ]);

        return back()->with('password_success', 'Contraseña actualizada correctamente.')->with('active_tab', 'password');
    }

    public function updateSocial(SocialUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update($request->validated());

        return back()->with('social_success', 'Redes sociales actualizadas correctamente.')->with('active_tab', 'social');
    }
}
