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
        // @todo this is yucky
        $possibility_1 = Friendship::where([
                'initiator_id' => $this->user_id,
                'recipient_id' => $this->friend_id,
            ])
            ->first();

        $possibility_2 = Friendship::where([
                'initiator_id' => $this->friend_id,
                'recipient_id' => $this->user_id,
            ])
            ->first();

        $friendship = $possibility_1 ?? $possibility_2;

        $friendship->status = 'rejected';
        $friendship->save();
    }
}
