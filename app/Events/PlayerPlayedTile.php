<?php

namespace App\Events;

use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class PlayerPlayedTile extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    #[StateId(PlayerState::class)]
    public int $player_id;

    public int $space;

    public string $direction;

    public function authorize()
    {
        $game = $this->state(GameState::class);

        $player = $this->state(PlayerState::class);

        $this->assert(
            $game->current_player_id === $this->player_id,
            'It is not this player '.$this->player_id.' turn'
        );

        $this->assert(
            $game->phase === $game::PHASE_PLACE_TILE,
            'It is time to move the elephant, not play a tile'
        );

        $this->assert(
            $player->hand > 0,
            'Player '.$this->player_id.' has no tiles'
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
        $this->assert(
            collect([1, 2, 3, 4, 5, 8, 9, 12, 13, 14, 15, 16])->contains($this->space),
            'Invalid space'
        );

        $this->assert(
            collect(['up', 'down', 'left', 'right'])->contains($this->direction),
            'Invalid direction'
        );

        $this->assert(
            $this->state(GameState::class)->slideIsBlockedByElephant($this->space, $this->direction) === false,
            'Elephant blocks this slide.'
        );
    }

    public function applyToGame(GameState $state)
    {
        $state->phase = GameState::PHASE_MOVE_ELEPHANT;
    }

    public function applyToPlayer(PlayerState $state)
    {
        $state->hand--;
    }

    public function fired()
    {
        $game = $this->state(GameState::class);

        $occupants = $game->slidingPositionOccupants($this->space, $this->direction);

        if ($occupants[3] && $occupants[2] && $occupants[1] && $occupants[0]) {
            TileReturnedToPlayer::fire(
                game_id: $this->game_id,
                player_id: $occupants[3],
            );
        }

        TilesSlid::fire(
            game_id: $this->game_id,
            player_id: $this->player_id,
            space: $this->space,
            direction: $this->direction,
        );

        $both_player_hands_are_empty = $game->currentPlayer()->hand === 0 && $game->idlePlayer()->hand === 0;

        if ($game->victor() || $both_player_hands_are_empty) {
            GameEnded::fire(game_id: $this->game_id);
        }
    }
}
