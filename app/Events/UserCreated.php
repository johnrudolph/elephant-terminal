<?php

namespace App\Events;

use App\Models\User;
use App\States\UserState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class UserCreated extends Event
{
    #[StateId(UserState::class)]
    public ?int $user_id = null;

    public string $name;

    public string $email;

    public string $password;

    public function apply(UserState $state)
    {
        $state->name = $this->name;
        $state->rating = 1000;
    }

    public function handle()
    {
        User::create([
            'id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);
    }
}
