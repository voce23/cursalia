<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::latest()->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return back()->with('success', 'Mensaje actualizado.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return to_route('admin.messages.index')->with('success', 'Mensaje eliminado.');
    }
}
