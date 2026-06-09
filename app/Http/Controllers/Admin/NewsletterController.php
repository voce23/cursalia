<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(): View
    {
        $subscriberCount = NewsletterSubscriber::count();
        $subscribers = NewsletterSubscriber::latest()->paginate(20);

        return view('admin.newsletter.index', compact('subscriberCount', 'subscribers'));
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:10'],
        ]);

        SendNewsletterJob::dispatch($request->subject, $request->body);

        flash()->success('El newsletter se está enviando en segundo plano.');

        return back();
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        flash()->success('Suscriptor eliminado.');

        return back();
    }
}
