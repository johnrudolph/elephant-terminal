<?php

namespace App\Events;

use App\Models\Game;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class GameStarted extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public function apply(GameState $state)
    {
        $state->status = 'active';

        // rand(0, 1) === 0
        //     ? $state->current_player_id = $state->player_1_id
        //     : $state->current_player_id = $state->player_2_id;

        $state->current_player_id = $state->player_1_id;

        $state->phase = GameState::PHASE_PLACE_TILE;
    }

    public function fired()
    {
        $state = $this->state(GameState::class);

        if ($state->currentPlayer()->is_bot) {
            // @todo fire bot move event
        }
    }

    public function handle()
    {
        $game = Game::find($this->game_id);

        $game->status = 'active';

        $game->save();
    }
}
