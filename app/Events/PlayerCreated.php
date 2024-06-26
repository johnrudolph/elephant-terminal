<?php

namespace App\Events;

use App\Models\Player;
use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class PlayerCreated extends Event
{
    #[StateId(PlayerState::class)]
    public ?int $player_id = null;

    #[StateId(GameState::class)]
    public int $game_id;

    public int $user_id;

    public ?bool $is_host = false;

    public ?bool $is_bot = false;

    public function apply(PlayerState $state)
    {
        $state->user_id = $this->user_id;

        $state->is_bot = $this->is_bot;

        $state->is_host = $this->is_host;
    }

    public function applyToGame(GameState $state)
    {
        $this->is_host
            ? $state->player_1_id = $this->player_id
            : $state->player_2_id = $this->player_id;
    }

    public function fired()
    {
        if (! $this->is_host) {
            GameStarted::fire(game_id: $this->game_id);
        }
    }

    public function handle()
    {
        Player::create([
            'id' => $this->player_id,
            'game_id' => $this->game_id,
            'user_id' => $this->user_id,
            'is_host' => $this->is_host,
            'is_bot' => $this->is_bot,
        ]);
    }
}
