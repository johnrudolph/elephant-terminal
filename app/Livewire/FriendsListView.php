<?php

namespace App\Livewire;

use Flux\Flux;
use App\Models\User;
use Livewire\Component;
use App\Models\Friendship;
use App\Events\UserAddedFriend;
use Livewire\Attributes\Computed;
use App\Events\UserRejectedFriend;

class FriendsListView extends Component
{
    public string $invitee_email;

    #[Computed]
    public function user()
    {
        return auth()->user();
    }

    #[Computed]
    public function friends()
    {
        return $this->user->friends();
    }

    #[Computed]
    public function outgoingRequests()
    {
        return Friendship::where('initiator_id', $this->user->id)
            ->where('status', 'pending')
            ->get();
    }

    #[Computed]
    public function incomingRequests()
    {
        return Friendship::where('recipient_id', $this->user->id)
            ->where('status', 'pending')
            ->get();
    }

    public function rules()
    {
        return [
            'invitee_email' => [
                'required',
                'email',
                'exists:users,email',
                'not_in:' . $this->user->email,
            ],
        ];
    }

    public function messages()
    {
        return [
            'invitee_email.required' => 'Please enter an email address.',
            'invitee_email.email' => 'Please enter a valid email address.',
            'invitee_email.exists' => 'This email is not registered with any user.',
            'invitee_email.not_in' => 'You cannot send a friend request to yourself.',
        ];
    }

    public function inviteFriend()
    {
        $this->validate();

        $invitee = User::where('email', $this->invitee_email)->first();

        if ($this->user->friendships->where('recipient_id', $invitee->id)->count() > 0) {
            $this->invitee_email = '';
            return;
        }

        UserAddedFriend::fire(
            user_id: $this->user->id,
            friend_id: $invitee->id,
        );
        
        $this->invitee_email = '';

        Flux::toast('Invitation sent');
    }

    public function acceptFriendship(string $friend_email)
    {
        $friend = User::where('email', $friend_email)->first();

        UserAddedFriend::fire(
            user_id: $this->user->id,
            friend_id: $friend->id,
        );

        Flux::toast('Invitation accepted');
    }

    public function rejectFriendship(string $friend_email)
    {
        $friend = User::where('email', $friend_email)->first();

        UserRejectedFriend::fire(
            user_id: $this->user->id,
            friend_id: $friend->id,
        );

        Flux::toast('Invitation rejected');
    }

    public function render()
    {
        return view('livewire.friends-list-view');
    }
}
