<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * protected @var int $id
     * protected @var object $notifiable
     */
    protected $_id, $_notifiable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $notifiable)
    {
        $this->_id = $id;
        $this->_notifiable = $notifiable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
