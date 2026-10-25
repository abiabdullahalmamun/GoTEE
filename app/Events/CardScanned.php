<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardScanned implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $cardId;
    public $deviceId;

    public function __construct($cardId, $deviceId)
    {
        $this->cardId = $cardId;
        $this->deviceId = $deviceId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('qc-entry'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'card.scanned';
    }
}