<?php

namespace App\Listners;

use App\Events\VehicleTypeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VehicleTypeListner
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
     * @param  VehicleTypeEvent  $event
     * @return void
     */
    public function handle(VehicleTypeEvent $event)
    {
        //
    }
}
