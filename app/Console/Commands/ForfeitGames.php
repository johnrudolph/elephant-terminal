<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Jobs\ForfeitGamesJob;
use Illuminate\Console\Command;

class ForfeitGames extends Command
{
    protected $signature = 'games:forfeit-games';
    protected $description = 'Forfeit unfinished games that have been abandoned';

    public function handle()
    {
        $games_to_forfeit = Game::where('status', 'active')
            ->where(function ($query) {
                // Games with moves where last elephant move is over 1 minute old
                $query->whereHas('moves', function ($q) {
                    $q->where('type', 'elephant')
                        ->where('created_at', '<', now()->subMinutes(1))
                        ->latest();
                })
                // OR games with no moves that are over 1 minute old
                ->orWhere(function ($q) {
                    $q->whereDoesntHave('moves')
                        ->where('created_at', '<', now()->subMinutes(1));
                });
            })
            ->get();

        if ($games_to_forfeit->count() > 0) {
            ForfeitGamesJob::dispatch($games_to_forfeit);
            $this->info("{$games_to_forfeit->count()} games found and queued for forfeiting.");
        } else {
            $this->info('No games found to forfeit.');
        }
    }
}
