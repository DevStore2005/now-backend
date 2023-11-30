<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\ServiceRequest;
use App\Mail\RequestAcceptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RequestAcceptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serviceRequest, $email, $provider;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ServiceRequest $serviceRequest, string $email, User $provider)
    {
        $this->serviceRequest = $serviceRequest;
        $this->provider = $provider;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new RequestAcceptMail($this->serviceRequest, $this->provider));
    }
}
