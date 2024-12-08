<?php

namespace App\Jobs;

use App\Events\GameAbandoned;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessStaleGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stale_games;

    public function __construct(Collection $stale_games)
    {
        $this->stale_games = $stale_games;
    }

    public function handle()
    {
        foreach ($this->stale_games as $game) {
            GameAbandoned::fire(game_id: $game->id);
        }
    }
}
