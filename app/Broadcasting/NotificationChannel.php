<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;

class NotificationChannel
{
    public $data;

    /**
     * Create a new channel instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool|null
     */
    public function join(User $user)
    {
        //
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        //
    }
}
