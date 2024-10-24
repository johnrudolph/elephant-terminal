<?php

namespace App\Models;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Move extends Model
{
    protected $guarded = [];

    protected $casts = [
        'initial_slide' => 'array',
        'board_before' => 'array',
        'board_after' => 'array',
        'bot_move_scores' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
