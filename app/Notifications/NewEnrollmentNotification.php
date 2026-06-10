<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Course $course,
        public readonly User $student,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_enrollment',
            'message' => "El estudiante \"{$this->student->name}\" se matriculó en \"{$this->course->title}\".",
            'course_id' => $this->course->id,
            'course_slug' => $this->course->slug,
            'student_id' => $this->student->id,
        ];
    }
}
