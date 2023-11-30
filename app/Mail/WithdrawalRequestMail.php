<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $provider, $amount, $description;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider, $amount, $description)
    {
        $this->provider = $provider;
        $this->amount = $amount;
        $this->description = $description;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Farenow!')
            ->markdown('emails.welcome')
            ->with([
                'provider' => $this->provider,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);
    }
}
