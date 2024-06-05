<?php

namespace App\Events;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class PlayerMovedElephant extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    #[StateId(PlayerState::class)]
    public int $player_id;

    public int $space;

    public function authorize()
    {
        $game = $this->state(GameState::class);

        $this->assert(
            $game->current_player_id === $this->player_id,
            'It is not this player '.$this->player_id.' turn'
        );

        $this->assert(
            $game->phase === $game::PHASE_MOVE_ELEPHANT,
            'It is time to place a tile, not move the elephant'
        );

        $this->assert(
            $game->player_1_id === $this->player_id || $game->player_2_id === $this->player_id,
            'Player '.$this->player_id.' is not in this game'
        );

        $this->assert(
            $game->status === 'active',
            'Game '.$this->game_id.' is not active'
        );
    }

    public function validate()
    {
        $game = $this->state(GameState::class);

        $this->assert(
            collect(range(1, 16))->contains($this->space),
            'Invalid space'
        );

        $this->assert(
            collect($game->validElephantMoves())->contains($this->space),
            'Elephant cannot reach that space'
        );
    }

    public function applyToGame(GameState $state)
    {
        $state->elephant_position = $this->space;

        $state->phase = GameState::PHASE_PLACE_TILE;

        if ($state->idlePlayer()->hand > 0) {
            $state->current_player_id = $state->idlePlayer()->id;
        }
    }

    public function applyToPlayer(PlayerState $state)
    {
        // @todo why do I need this function
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
}
