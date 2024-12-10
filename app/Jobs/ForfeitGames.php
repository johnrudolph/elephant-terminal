<?php

namespace App\Jobs;

use App\Models\Game;
use App\Events\GameAbandoned;
use App\Events\GameForfeited;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForfeitGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $abandoned_games;

    public function __construct(Collection $stale_games)
    {
        $this->abandoned_games = Game::where('status', 'active')
            ->filter(function ($game) {
                return $game->moves->filter(fn($move) => $move->type === 'elephant')
                    ->last()
                    ->created_at < now()->subMinutes(1);
            })
            ->get();
    }

    public function handle()
    {
        foreach ($this->abandoned_games as $game) {
            $winner_id = $game->moves->filter(fn($move) => $move->type === 'elephant')
                ->last()
                ->player_id;

            $loser_id = $game->players->firstWhere('id', '!=', $winner_id)->id;

            GameForfeited::fire(
                game_id: $game->id, 
                winner_id: $winner_id, 
                loser_id: $loser_id
            );
        }
    }
}
