<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowingRejected extends Notification
{
    use Queueable;

    public function __construct(public Borrowing $borrowing) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'borrowing_rejected',
            'borrowing_id' => $this->borrowing->id,
            'message' => 'Pengajuan Anda ditolak.',
        ];
    }
}
