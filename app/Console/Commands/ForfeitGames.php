<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\Player;
use App\Jobs\ForfeitGamesJob;
use Illuminate\Console\Command;

class ForfeitGames extends Command
{
    protected $signature = 'games:forfeit-games';
    protected $description = 'Forfeit unfinished games that have been abandoned';

    public function handle()
    {
        $players_to_forfeit = Player::whereNotNull('forfeits_at')
            ->where('forfeits_at', '<', now())
            ->get();

        if ($players_to_forfeit->count() > 0) {
            ForfeitGamesJob::dispatch($players_to_forfeit);
            $this->info("{$players_to_forfeit->count()} players found and queued for forfeiting.");
        } else {
            $this->info('No players found to forfeit.');
        }
    }
}
