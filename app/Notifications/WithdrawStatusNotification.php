<?php

namespace App\Notifications;

use App\Models\Withdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WithdrawStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Withdraw $withdraw) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->withdraw->status === 'approved' ? 'aprobado' : 'rechazado';

        $data = [
            'type' => 'withdraw_status',
            'message' => "Tu retiro de \${$this->withdraw->amount} ha sido {$status}.",
            'withdraw_id' => $this->withdraw->id,
            'status' => $this->withdraw->status,
        ];

        if ($this->withdraw->status === 'rejected' && $this->withdraw->rejection_reason) {
            $data['reason'] = $this->withdraw->rejection_reason;
        }

        return $data;
    }
}
