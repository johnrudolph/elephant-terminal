<?php

namespace App\Jobs;

use App\Events\GameForfeited;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForfeitGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $forfeiting_players;

    public function __construct(Collection $forfeiting_players)
    {
        $this->forfeiting_players = $forfeiting_players;
    }

    public function handle()
    {
        foreach ($this->forfeiting_players as $player) {
            $game = $player->game;

            $loser_id = (int) $game->current_player_id;
            $winner_id = (int) $game->players->firstWhere('id', '!=', $loser_id)->id;

            GameForfeited::fire(
                game_id: $game->id, 
                winner_id: $winner_id, 
                loser_id: $loser_id
            );
        }
    }
}
