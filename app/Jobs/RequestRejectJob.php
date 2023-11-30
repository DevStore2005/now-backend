<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\ServiceRequest;
use App\Mail\RequestRejectMail;
use App\Http\Middleware\Provider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RequestRejectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $serviceRequest, $user, $provider, $refund;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        ServiceRequest $serviceRequest,
        User $user,
        User $provider,
        $refund
    )
    {
        $this->serviceRequest = $serviceRequest;
        $this->user = $user;
        $this->provider = $provider;
        $this->refund = $refund;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)
            ->send(new RequestRejectMail(
                $this->serviceRequest,
                $this->provider,
                $this->user,
                $type = 'User',
                $this->refund
            ));
        Mail::to($this->provider->email)
            ->send(new RequestRejectMail(
                $this->serviceRequest,
                $this->provider,
                $this->user,
                $type = 'Provider'
            ));
    }
}
