<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\ServiceRequest;
use App\Mail\NewServiceRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewServiceRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ServiceRequest $serviceRequest 
     *
     */
    public $serviceRequest;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->serviceRequest->provider->email)->send(new NewServiceRequestMail($this->serviceRequest, 'provider'));
        Mail::to($this->serviceRequest->user->email)->send(new NewServiceRequestMail($this->serviceRequest, 'user'));
    }
}
