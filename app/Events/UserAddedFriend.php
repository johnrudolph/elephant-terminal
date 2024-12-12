<?php

namespace App\Events;

use App\Models\User;
use Thunk\Verbs\Event;
use App\States\UserState;
use App\Models\Friendship;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;

class UserAddedFriend extends Event
{
    #[StateId(UserState::class)]
    public int $user_id;

    public int $friend_id;

    public function handle()
    {

        $existing_friendship = Friendship::where('initiator_id', $this->friend_id)
            ->where('recipient_id', $this->user_id)
            ->first();

        if ($existing_friendship) {
            $existing_friendship->update(['status' => 'accepted']);
            UserAddedFriendBroadcast::dispatch(User::find($this->user_id), User::find($this->friend_id));
            return;
        }

        Friendship::create([
            'initiator_id' => $this->user_id,
            'recipient_id' => $this->friend_id,
        ]);

        UserAddedFriendBroadcast::dispatch(User::find($this->user_id), User::find($this->friend_id));
    }
}
