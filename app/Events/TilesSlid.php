<?php

namespace App\Events;

use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class TilesSlid extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public int $player_id;

    public int $space;

    public string $direction;

    public function applyToGame(GameState $state)
    {
        $sliding_positions = $state->slidingPositions($this->space, $this->direction);

        $occupants = $state->slidingPositionOccupants($this->space, $this->direction);

        if ($occupants[2] && $occupants[1] && $occupants[0]) {
            $state->board[$sliding_positions[3]] = $occupants[2];
        }

        if ($occupants[1] && $occupants[0]) {
            $state->board[$sliding_positions[2]] = $occupants[1];
        }

        if ($occupants[0]) {
            $state->board[$sliding_positions[1]] = $occupants[0];
        }

        $state->board[$this->space] = $this->player_id;
    }
}
