<?php

namespace App\Events;

use Thunk\Verbs\Event;
use App\States\UserState;
use App\Models\Friendship;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class UserRejectedFriend extends Event
{
    #[StateId(UserState::class)]
    public int $user_id;

    public int $friend_id;

    public function handle()
    {
        Friendship::betweenUsers($this->user_id, $this->friend_id)
            ->update(['status' => 'rejected']);
    }
}
