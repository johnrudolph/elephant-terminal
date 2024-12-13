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

        $state->victor_ids = $state->victors($state->board);

        $state->winning_spaces = collect($state->victor_ids)
            ->map(fn($v_id) => $this->state(GameState::class)->winningSpaces(PlayerState::load($v_id), $state->board))
            ->values()
            ->flatten()
            ->toArray();

        if ($state->is_ranked) {
            $state->players()->each(fn($p) => $p->user()->rating = $this->calculateNewRating($p, $state));
        }
    }

    public function handle()
    {
        $game = Game::find($this->game_id);

        $game->status = 'complete';

        $game->victor_ids = $this->state(GameState::class)->victor_ids;

        $game->winning_spaces = $this->state(GameState::class)->winning_spaces;

        $game->save();

        $game->players->each(function ($player) {
            $player->forfeits_at = null;
            $player->save();

            $user = $player->user;

            $user->rating = $user->state()->rating;
            $user->save();
        });

        GameEndedBroadcast::dispatch($game);
    }

    public function calculateNewRating(PlayerState $player, GameState $game): int
    {
        $k_factor = 32;
        $player_rating = $player->user()->rating;
        $opponent_rating = $player->opponent()->user()->rating;

        $expected_score = 1 / (1 + (10 ** (($opponent_rating - $player_rating) / 400)));

        $actual_score = match(true) {
            count($game->victor_ids) === 1 && in_array($player->id, $game->victor_ids) => 1.0,
            count($game->victor_ids) === 1 => 0.0,
            default => 0.5 // draw
        };

        $new_rating = $player_rating + ($k_factor * ($actual_score - $expected_score));
        
        return max(100, min(3000, round($new_rating)));
    }
}
