<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeMiscSectionUpdateRequest;
use App\Models\HomeMiscSection;
use Illuminate\Http\RedirectResponse;

class HomeMiscSectionController extends Controller
{
    public function update(HomeMiscSectionUpdateRequest $request): RedirectResponse
    {
        $section = HomeMiscSection::firstOrCreate(['id' => 1]);
        $section->update($request->validated());

        flash()->success('Newsletter, banner y video actualizados correctamente.');

        return redirect()->route('admin.home-sections.index');
    }
}
