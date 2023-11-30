<?php

namespace App\Listners;

use App\Events\MessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MessageListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageEvent  $event
     * @return void
     */
    public function handle(MessageEvent $event)
    {
        //
    }
}
