<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\GameCanceledBroadcast;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameCanceled extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public function apply(GameState $state)
    {
        $state->status = 'canceled';
    }

    public function handle()
    {
        $game = Game::find($this->game_id);
        $game->status = 'canceled';
        $game->save();

        GameCanceledBroadcast::dispatch($game);
    }
}
