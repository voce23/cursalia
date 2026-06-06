<?php

namespace App\Notifications;

use App\Models\Withdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewWithdrawRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Withdraw $withdraw) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'new_withdraw_request',
            'message'     => "Nuevo retiro solicitado por \"{$this->withdraw->user->name}\" por \${$this->withdraw->amount}.",
            'withdraw_id' => $this->withdraw->id,
            'user_id'     => $this->withdraw->user_id,
            'amount'      => $this->withdraw->amount,
        ];
    }
}
