<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ServiceRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestAcceptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest, $provider;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest, User $provider)
    {
        $this->serviceRequest = $serviceRequest;
        $this->provider = $provider;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your request has been accepted!')
            ->markdown('emails.request-accept')
            ->with([
                'serviceRequest' => $this->serviceRequest,
                'provider' => $this->provider,
            ]);
    }
}
