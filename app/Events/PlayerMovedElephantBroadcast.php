<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Move;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerMovedElephantBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Game $game, public Move $move)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting elephant move on channel:', ['channel' => "private-games.{$this->game->id}"]);
        return [
            new PrivateChannel('games.'.$this->game->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'move_id' => $this->move->id,
        ];
    }
}
