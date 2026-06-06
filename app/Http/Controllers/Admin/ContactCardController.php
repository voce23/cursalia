<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactCardRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactCardController extends Controller
{
    public function index(): View
    {
        $items = Contact::query()->orderBy('sort_order')->paginate(20);

        return view('admin.contact-cards.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.contact-cards.create');
    }

    public function store(ContactCardRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) $request->boolean('is_active');

        Contact::create($data);

        flash()->success('Tarjeta de contacto creada correctamente.');

        return redirect()->route('admin.contact-cards.index');
    }

    public function edit(Contact $contactCard): View
    {
        return view('admin.contact-cards.edit', ['item' => $contactCard]);
    }

    public function update(ContactCardRequest $request, Contact $contactCard): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) $request->boolean('is_active');

        $contactCard->update($data);

        flash()->success('Tarjeta de contacto actualizada correctamente.');

        return redirect()->route('admin.contact-cards.index');
    }

    public function destroy(Contact $contactCard): RedirectResponse
    {
        $contactCard->delete();

        flash()->success('Tarjeta de contacto eliminada correctamente.');

        return redirect()->route('admin.contact-cards.index');
    }
}
