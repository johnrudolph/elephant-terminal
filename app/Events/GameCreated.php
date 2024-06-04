<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use App\Events\PlayerCreated;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

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

        $state->board = collect(range(0, 15))->map(fn($i) => null);
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
                // @todo do we need a bot user? does it matter?
                user_id: 1,
                is_host: false,
                is_bot: true,
                bot_difficulty: $this->bot_difficulty,
            );
        }
    }

    public function handle()
    {
        Game::create([
            'id' => $this->game_id,
            'code' => $this->generateGameCode(),
            'status' => 'created',
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
