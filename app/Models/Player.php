<?php

namespace App\Models;

use App\Events\PlayerMovedElephant;
use App\Events\PlayerPlayedTile;
use App\States\PlayerState;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory, HasSnowflakes;

    protected $guarded = [];

    public function state()
    {
        return PlayerState::load($this->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function playTile(int $space, string $direction)
    {
        PlayerPlayedTile::fire(
            game_id: $this->game->id,
            player_id: $this->id,
            space: $space,
            direction: $direction,
        );
    }

    public function moveElephant(int $space)
    {
        PlayerMovedElephant::fire(
            game_id: $this->game->id,
            player_id: $this->id,
            space: $space,
        );
    }
}
