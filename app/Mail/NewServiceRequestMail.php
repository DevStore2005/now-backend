<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ServiceRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewServiceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var ServiceRequest $serviceRequest 
     * @var string $to_user
     */
    public $serviceRequest, $to_user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest, string $to_user = 'provider')
    {
        $this->serviceRequest = $serviceRequest;
        $this->to_user = $to_user;
    }

    /**
     * Build the message.
     *
     * @return 
     */
    public function build()
    {
        // $this->view('emails.service-request', [
        //     'serviceRequest' => $this->serviceRequest
        // ]);
        return $this->subject('New Service Request')
            ->markdown('emails.service-request')
            ->with([
                'serviceRequest' => $this->serviceRequest,
                'to_user' => $this->to_user
            ]);
    }
}
