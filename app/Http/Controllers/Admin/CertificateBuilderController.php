<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CertificateBuilderUpdateRequest;
use App\Models\CertificateBuilder;
use App\Models\CertificateBuilderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateBuilderController extends Controller
{
    private const DEFAULT_ITEMS = [
        'title' => ['x_position' => 170, 'y_position' => 130],
        'subtitle' => ['x_position' => 170, 'y_position' => 225],
        'description' => ['x_position' => 170, 'y_position' => 315],
        'signature' => ['x_position' => 840, 'y_position' => 560],
    ];

    public function index(): View
    {
        $certificate = CertificateBuilder::firstOrCreate(['id' => 1]);

        foreach (self::DEFAULT_ITEMS as $elementId => $position) {
            CertificateBuilderItem::firstOrCreate(
                ['element_id' => $elementId],
                $position,
            );
        }

        $items = CertificateBuilderItem::query()
            ->whereIn('element_id', array_keys(self::DEFAULT_ITEMS))
            ->get()
            ->keyBy('element_id');

        return view('admin.certificate-builder.index', compact('certificate', 'items'));
    }

    public function update(CertificateBuilderUpdateRequest $request): RedirectResponse
    {
        $certificate = CertificateBuilder::firstOrCreate(['id' => 1]);

        $data = [
            'title' => $request->string('title')->toString(),
            'sub_title' => $request->string('subtitle')->toString(),
            'description' => $request->string('description')->toString(),
        ];

        if ($request->hasFile('background')) {
            if ($certificate->background) {
                Storage::disk('public')->delete($certificate->background);
            }

            $data['background'] = $request->file('background')->store('certificates', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($certificate->signature) {
                Storage::disk('public')->delete($certificate->signature);
            }

            $data['signature'] = $request->file('signature')->store('certificates', 'public');
        }

        $certificate->update($data);

        flash()->success('Constructor de certificados actualizado correctamente.');

        return redirect()->route('admin.certificate-builder.index');
    }

    public function updateItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'element_id' => ['required', 'in:title,subtitle,description,signature'],
            'x_position' => ['required', 'integer', 'min:0', 'max:2000'],
            'y_position' => ['required', 'integer', 'min:0', 'max:2000'],
        ]);

        CertificateBuilderItem::updateOrCreate(
            ['element_id' => $validated['element_id']],
            [
                'x_position' => $validated['x_position'],
                'y_position' => $validated['y_position'],
            ],
        );

        return response()->json(['success' => true]);
    }
}
