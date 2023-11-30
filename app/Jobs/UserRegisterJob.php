<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\RegisteredMail;
use Illuminate\Bus\Queueable;
use App\Mail\UserRegisterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UserRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user 
     *
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new UserRegisterMail($this->user));
        Mail::to(config('mail.to.marketing'))->send(new RegisteredMail($this->user));
    }
}
