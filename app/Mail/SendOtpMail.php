<?php

namespace App\Mail;

use App\Models\PhoneVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Vonage\Voice\Endpoint\Phone;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var PhoneVerification $otp
     *
     */
    public $otp, $type;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PhoneVerification $otp, $type = null)
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type) {
            return $this->subject('Forgot password')
            ->markdown('emails.forgot-otp')
            ->with([
                'otp' => $this->otp,
            ]);
        }
        return $this->subject('Farenow Otp!')
            ->markdown('emails.otp')
            ->with([
                'otp' => $this->otp,
            ]);
    }
}
