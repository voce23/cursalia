<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewInstructorRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $applicant) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_instructor_request',
            'message' => "\"{$this->applicant->name}\" ha solicitado convertirse en instructor.",
            'applicant_id' => $this->applicant->id,
        ];
    }
}
