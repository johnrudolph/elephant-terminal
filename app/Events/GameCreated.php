<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Thunk\Verbs\Event;
use App\States\GameState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameCreated extends Event
{
    #[StateId(GameState::class)]
    public ?int $game_id = null;

    public int $user_id;

    public ?bool $is_single_player = false;

    public ?string $bot_difficulty = null;

    public ?string $victory_shape = null;

    public function apply(GameState $state)
    {
        $state->status = 'created';

        $state->is_single_player = $this->is_single_player;

        $state->victors = [];

        $state->moves = [];

        $state->phase = $state::PHASE_PLACE_TILE;
    }

    public function fired()
    {
        if (! $this->victory_shape) {
            $this->victory_shape = collect([
                'square', 
                'line', 
                // 'pyramid', 
                'el', 
                'zig'
            ])->random();
        }

        PlayerCreated::fire(
            game_id: $this->game_id,
            user_id: $this->user_id,
            is_host: true,
            is_bot: false,
            victory_shape: $this->victory_shape,
        );

        if ($this->is_single_player) {
            PlayerCreated::fire(
                game_id: $this->game_id,
                // @todo this is yucky
                user_id: User::firstWhere('email', 'bot@bot.bot')->id,
                is_host: false,
                is_bot: true,
                bot_difficulty: $this->bot_difficulty,
                victory_shape: $this->victory_shape,
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
