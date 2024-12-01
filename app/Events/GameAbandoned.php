<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameAbandoned extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public function apply(GameState $state)
    {
        $state->status = 'abandoned';
    }

    public function handle()
    {
        $game = Game::find($this->game_id);
        $game->status = 'abandoned';
        $game->save();
    }
}
