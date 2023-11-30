<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ServiceRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestRejectMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest, $user, $provider, $type, $refund;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest, User $provider, User $user, $type, $refund = null)
    {
        $this->serviceRequest = $serviceRequest;
        $this->provider = $provider;
        $this->user = $user;
        $this->type = $type;
        $this->refund = $refund;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your request has been rejected!')
            ->markdown('emails.request-reject')
            ->with([
                'refund' => $this->refund,
                'serviceRequest' => $this->serviceRequest,
                'provider' => $this->provider,
                'user' => $this->user,
                'type' => $this->type,
            ]);
    }
}
