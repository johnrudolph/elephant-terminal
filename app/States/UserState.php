<?php

namespace App\States;

use App\Models\User;
use Thunk\Verbs\State;
use App\States\GameState;
use Illuminate\Support\Collection;

class UserState extends State
{
    public string $name;

    public string $email;

    public string $encrypted_password;

    public array $game_ids = [];

    public array $player_ids = [];

    public function model(): User
    {
        return User::find($this->id);
    }

    public function games(): Collection
    {
        return collect($this->game_ids)->map(fn ($id) => GameState::load($id));
    }

    public function players(): Collection
    {
        return collect($this->player_ids)->map(fn ($id) => PlayerState::load($id));
    }
}
