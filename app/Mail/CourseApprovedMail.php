<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Course $course) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu curso ha sido aprobado!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.course-approved',
        );
    }
}
