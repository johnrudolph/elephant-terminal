<?php

namespace App\Events;

use App\Models\Game;
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
    public function __construct(public readonly Game $game)
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
        return [
            new PrivateChannel('games.'.$this->game->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'new_elephant_space' => $this->game->elephant_space,
            'current_player_id_string' => (string) $this->game->current_player_id,
            'valid_elephant_moves' => $this->game->valid_elephant_moves,
            'valid_slides' => $this->game->valid_slides,
        ];
    }
}
