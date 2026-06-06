<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriberController extends Controller
{
    public function store(NewsletterSubscribeRequest $request): RedirectResponse
    {
        NewsletterSubscriber::firstOrCreate([
            'email' => $request->string('email')->toString(),
        ]);

        flash()->success('Tu suscripción al newsletter se registró correctamente.');

        return back();
    }
}
