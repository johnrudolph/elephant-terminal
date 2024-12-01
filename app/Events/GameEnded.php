<?php

namespace App\Events;

use App\Models\Game;
use Thunk\Verbs\Event;
use App\States\GameState;
use App\States\PlayerState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class GameEnded extends Event
{
    #[StateId(GameState::class)]
    public int $game_id;

    public function apply(GameState $state)
    {
        $state->status = 'complete';

        $state->victors = $state->victor($state->board);

        if ($state->is_ranked) {
            $state->players()->each(fn($p) => $p->rating = $this->calculateNewRating($p, $state));
        }
    }

    public function handle()
    {
        $game = Game::find($this->game_id);

        $game->status = 'complete';

        $game->victors = $this->state(GameState::class)->victors;

        $game->save();

        $game->players()->each(function ($player) {
            $user_state = $player->user;
            $user_model = $user_state->model();

            $user_model->rating = $user_state->rating;
            $user_model->save();
        });
    }

    public function calculateNewRating(PlayerState $player, GameState $game): int
    {
        // maybe dynamically set k_factor based on experience later?
        $k_factor = 32;
        $player_rating = $player->user()->rating;
        $opponent_rating = $player->opponent()->user()->rating;

        $expected_score = 1 / (1 + (10 ** (($opponent_rating - $player_rating) / 400)));

        $actual_score = match(true) {
            count($game->victors) === 1 && in_array($player->id, $game->victors) => 1.0,
            count($game->victors) === 1 => 0.0,
            default => 0.5 // draw
        };

        $new_rating = $player_rating + ($k_factor * ($actual_score - $expected_score));
        
        return max(100, min(3000, round($new_rating)));
    }
}
