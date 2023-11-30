<?php

namespace App\Jobs;

use App\Mail\SendOtpMail;
use Illuminate\Bus\Queueable;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $otp;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PhoneVerification $otp, $type = null)
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->otp->email)->send(new SendOtpMail($this->otp, $this->type));
    }
}
