<?php

namespace App\Events;

use App\Models\DriverLocation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly DriverLocation $location) {}

    public function broadcastOn(): Channel
    {
        return new Channel('driver-locations');
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'     => $this->location->user_id,
            'latitude'    => (float) $this->location->latitude,
            'longitude'   => (float) $this->location->longitude,
            'heading'     => $this->location->heading !== null ? (float) $this->location->heading : null,
            'speed'       => $this->location->speed !== null ? (float) $this->location->speed : null,
            'recorded_at' => $this->location->recorded_at->toISOString(),
        ];
    }
}
