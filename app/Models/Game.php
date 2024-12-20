<?php

namespace App\Models;

use App\Models\Move;
use App\States\GameState;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory, HasSnowflakes;

    protected $guarded = [];

    protected $casts = [
        'board' => 'array',
        'valid_slides' => 'array',
        'valid_elephant_moves' => 'array',
        'victor_ids' => 'array',
        'winning_spaces' => 'array',
    ];

    public function state()
    {
        return GameState::load($this->id);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}
