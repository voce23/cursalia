<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeaturedInstructorRequest;
use App\Models\FeaturedInstructor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeaturedInstructorController extends Controller
{
    public function index(): View
    {
        $items = FeaturedInstructor::query()
            ->with('user')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.featured-instructors.index', compact('items'));
    }

    public function create(): View
    {
        $instructors = User::query()
            ->where('instructor_request', true)
            ->where('approve_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.featured-instructors.create', compact('instructors'));
    }

    public function store(FeaturedInstructorRequest $request): RedirectResponse
    {
        $request->validate([
            'user_id' => [Rule::unique('featured_instructors', 'user_id')],
        ]);

        FeaturedInstructor::create([
            'user_id' => (int) $request->input('user_id'),
            'sort_order' => (int) $request->input('sort_order'),
            'is_active' => (bool) $request->boolean('is_active'),
        ]);

        flash()->success('Instructor destacado agregado correctamente.');

        return redirect()->route('admin.featured-instructors.index');
    }

    public function edit(FeaturedInstructor $featuredInstructor): View
    {
        $instructors = User::query()
            ->where('instructor_request', true)
            ->where('approve_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.featured-instructors.edit', [
            'item' => $featuredInstructor,
            'instructors' => $instructors,
        ]);
    }

    public function update(FeaturedInstructorRequest $request, FeaturedInstructor $featuredInstructor): RedirectResponse
    {
        $request->validate([
            'user_id' => [Rule::unique('featured_instructors', 'user_id')->ignore($featuredInstructor->id)],
        ]);

        $featuredInstructor->update([
            'user_id' => (int) $request->input('user_id'),
            'sort_order' => (int) $request->input('sort_order'),
            'is_active' => (bool) $request->boolean('is_active'),
        ]);

        flash()->success('Instructor destacado actualizado correctamente.');

        return redirect()->route('admin.featured-instructors.index');
    }

    public function destroy(FeaturedInstructor $featuredInstructor): RedirectResponse
    {
        $featuredInstructor->delete();

        flash()->success('Instructor destacado eliminado correctamente.');

        return redirect()->route('admin.featured-instructors.index');
    }
}
