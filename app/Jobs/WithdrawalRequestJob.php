<?php

namespace App\Jobs;

use App\Models\User;
use App\Utils\UserType;
use Illuminate\Bus\Queueable;
use App\Mail\WithdrawalRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WithdrawalRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $provider, $amount, $description;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::whereRole(UserType::ADMIN)->first();

        Mail::to($user->email)
            ->cc($this->provider->email)
            ->send(new WithdrawalRequestMail($this->provider, $this->amount, $this->description));
    }
}
