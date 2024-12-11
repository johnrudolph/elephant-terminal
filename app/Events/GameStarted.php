<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\GameStartedBroadcast;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameStarted extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public function apply(GameState $state)
    {
        $state->status = 'active';

        $state->current_player_id = $state->player_1_id;

        $state->phase = GameState::PHASE_PLACE_TILE;

        $state->victor_ids = [];
    }

    public function fired()
    {
        $game = $this->state(GameState::class);

        if ($game->currentPlayer()->is_bot) {
            $bot_tile_move = $game->selectBotTileMove($game->board);

            PlayerPlayedTile::fire(
                game_id: $this->game_id,
                player_id: $game->current_player_id,
                space: $bot_tile_move['space'],
                direction: $bot_tile_move['direction']
            );

            $bot_elephant_move = $game->selectBotElephantMove($game->board);

            PlayerMovedElephant::fire(
                game_id: $this->game_id,
                player_id: $game->current_player_id,
                space: $bot_elephant_move
            );
        }
    }

    public function handle()
    {
        $game = Game::find($this->game_id);
        $game->status = 'active';
        $game->save();

        GameStartedBroadcast::dispatch($game);
    }
}
