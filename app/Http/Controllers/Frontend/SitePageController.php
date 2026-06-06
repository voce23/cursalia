<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactMessageRequest;
use App\Mail\ContactMail;
use App\Models\AboutSection;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\ContactSetting;
use App\Models\Counter;
use App\Models\CustomPage;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function about(): View
    {
        $about        = AboutSection::query()->first();
        $counter      = Counter::query()->first();
        $testimonials = Testimonial::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('frontend.pages.about', compact('about', 'counter', 'testimonials'));
    }

    public function contact(): View
    {
        $contactCards   = Contact::query()->where('is_active', true)->orderBy('sort_order')->get();
        $contactSetting = ContactSetting::query()->first();

        return view('frontend.pages.contact', compact('contactCards', 'contactSetting'));
    }

    public function sendContact(ContactMessageRequest $request): RedirectResponse
    {
        $contactSetting = ContactSetting::query()->first();
        $receiver = $contactSetting?->receiver_email ?: config('mail.from.address');

        Mail::to($receiver)->queue(new ContactMail(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            subjectLine: $request->string('subject')->toString(),
            messageBody: $request->string('message')->toString(),
        ));

        return back()->with('success', '¡Gracias! Tu mensaje fue enviado, te responderemos muy pronto.');
    }

    public function customPage(string $slug): View
    {
        $page = CustomPage::query()
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return view('frontend.pages.custom-page', compact('page'));
    }

    /**
     * Página /sobre-el-autor: ficha pública del admin principal (autor del blog).
     * Crítica para E-E-A-T de Google: foto, bio, credenciales, redes verificables,
     * lista de artículos publicados. Sin esto Google asume "fuente desconocida".
     */
    public function author(): View
    {
        // Por ahora el "autor del blog" = admin con id más bajo (creador).
        // Cuando haya multi-autor, esto se vuelve /autor/{slug}.
        $author = Admin::query()->orderBy('id')->firstOrFail();

        $posts = Blog::query()
            ->where('admin_id', $author->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with('category')
            ->latest('published_at')
            ->get();

        $courseLessons = $posts->filter(fn ($p) => $p->category?->slug === Blog::COURSE_CATEGORY_SLUG);
        $otherPosts    = $posts->reject(fn ($p) => $p->category?->slug === Blog::COURSE_CATEGORY_SLUG);

        return view('frontend.pages.author', compact('author', 'posts', 'courseLessons', 'otherPosts'));
    }
}
