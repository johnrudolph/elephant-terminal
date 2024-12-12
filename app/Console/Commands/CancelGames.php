<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Support\Carbon;
use App\Jobs\CancelGamesJob;
use Illuminate\Console\Command;

class CancelGames extends Command
{
    protected $signature = 'games:cancel-games';
    protected $description = 'Check for stale games that were created more than 5 minutes ago';

    public function handle()
    {
        $staleGames = Game::where('status', 'created')
            ->where('created_at', '<=', Carbon::now()->subMinutes(5))
            ->get();

        if ($staleGames->count() > 0) {
            CancelGamesJob::dispatch($staleGames);
            $this->info("{$staleGames->count()} stale games found and queued for cancellation.");
        } else {
            $this->info('No stale games found.');
        }
    }
}
