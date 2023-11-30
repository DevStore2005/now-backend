<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User $user 
     *
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Farenow!')
            ->markdown('emails.registered')
            ->with([
                'user' => $this->user,
            ]);
    }
}
