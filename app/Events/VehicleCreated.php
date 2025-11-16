<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The vehicle instance
     *
     * @var Vehicle
     */
    public $vehicle;

    /**
     * Create a new event instance.
     */
    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vehicles'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->vehicle->id,
            'registration_number' => $this->vehicle->registration_number,
            'brand' => $this->vehicle->brand->name ?? null,
            'model' => $this->vehicle->vehicleModel->name ?? null,
            'status' => $this->vehicle->status,
        ];
    }
}
