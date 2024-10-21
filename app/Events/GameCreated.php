<?php

namespace App\Events;

use App\Models\Game;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class GameCreated extends Event
{
    #[StateId(GameState::class)]
    public ?int $game_id = null;

    public int $user_id;

    public ?bool $is_single_player = false;

    public ?string $bot_difficulty = null;

    public function apply(GameState $state)
    {
        $state->status = 'created';

        $state->is_single_player = $this->is_single_player;

        $state->player_1_id = $this->user_id;

        $state->victors = [];

        $state->moves = [];

        $state->phase = $state::PHASE_PLACE_TILE;

        $state->current_player_id = $state->player_1_id;
    }

    public function fired()
    {
        PlayerCreated::fire(
            game_id: $this->game_id,
            user_id: $this->user_id,
            is_host: true,
            is_bot: false,
        );

        if ($this->is_single_player) {
            PlayerCreated::fire(
                game_id: $this->game_id,
                user_id: 1,
                is_host: false,
                is_bot: true,
                bot_difficulty: $this->bot_difficulty,
            );
        }
    }

    public function handle()
    {
        $game = $this->state(GameState::class);

        Game::create([
            'id' => $this->game_id,
            'code' => $this->generateGameCode(),
            'status' => 'created',
            'board' => $game->board,
            'valid_elephant_moves' => $game->validElephantMoves(),
            'valid_slides' => $game->validSlides(),
            'elephant_space' => $game->elephant_space,
            'phase' => $game->phase,
            'current_player_id' => $game->current_player_id,
            'victors' => $game->victors,
        ]);
    }

    private function generateGameCode()
    {
        $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 4);

        return Game::where('code', $code)->exists()
            ? $this->generateGameCode()
            : $code;
    }
}
