<?php

namespace App\Events;

use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class PlayerPlayedTile extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    #[StateId(PlayerState::class)]
    public int $player_id;

    // @todo this should be an enum?
    public int $space;

    // @todo this should be an enum?
    public string $direction;

    public function authorize()
    {
        // @todo check if it's the player's turn

        // @todo check if the player has tiles

        // @todo check if the player is in the game

        // @todo check if the game is active
    }

    public function validate()
    {
        // @todo check that they are not blocked by the elephant
    }

    public function apply(GameState $state)
    {
        $state->phase = GameState::PHASE_MOVE_ELEPHANT;
    }

    public function fired()
    {

    }
}
