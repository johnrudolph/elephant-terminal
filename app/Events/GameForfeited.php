<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\GameEndedBroadcast;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameForfeited extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public int $loser_id;

    public int $winner_id;

    public function apply(GameState $state)
    {
        $state->status = 'completed';

        $state->victor_ids = [$this->winner_id];
    }

    public function handle()
    {
        $game = Game::find($this->game_id);
        $game->status = 'completed';
        $game->victor_ids = [$this->winner_id];
        $game->save();

        GameEndedBroadcast::dispatch($game);
    }
}
