<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VehicleTypeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string $eventType
     */
    public $vehicleType, $eventType;

    /**
     * Create a new event instance.
     * @param string $eventType
     *
     * @return void
     */
    public function __construct($vehicleType, string $eventType)
    {
        $this->vehicleType = $vehicleType;
        $this->eventType = $eventType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("{$this->eventType}-vehicle-type");
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            "type" => $this->eventType,
            'vehicleType' => $this->vehicleType,
        ];
    }
}
