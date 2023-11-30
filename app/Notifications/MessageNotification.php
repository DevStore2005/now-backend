<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Jobs\FcmNotificationJob;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $_notification;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->_notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'database',
            // 'broadcast'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage|null
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->_notification->toArray();
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    // public function toBroadcast($notifiable)
    // {
    //     dispatch(new FcmNotificationJob($this->id, $notifiable))->delay(now()->addSeconds(10));
    //     return new BroadcastMessage($this->toArray($notifiable->toArray()));
    // }
}
