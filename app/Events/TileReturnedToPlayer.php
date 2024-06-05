<?php

namespace App\Events;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class TileReturnedToPlayer extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    #[StateId(PlayerState::class)]
    public int $player_id;

    public function applyToGame(GameState $state)
    {
        //
    }

    public function applyToPlayer(PlayerState $state)
    {
        $state->hand++;
    }
}
