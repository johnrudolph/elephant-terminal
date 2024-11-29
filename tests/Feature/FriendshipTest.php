<?php

use App\Models\User;
use App\Models\Friendship;
use App\Events\UserCreated;
use Thunk\Verbs\Facades\Verbs;
use App\Events\UserAddedFriend;
use App\Events\UserRejectedFriend;

beforeEach(function () {
    Verbs::commitImmediately();
});

it('makes friends', function () {
    $john_id = UserCreated::fire(
        name: 'John Doe', 
        email: 'john@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $john = User::find($john_id);

    $jane_id = UserCreated::fire(
        name: 'Jane Doe', 
        email: 'jane@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $jane = User::find($jane_id);

    UserAddedFriend::fire(
        user_id: $john_id,
        friend_id: $jane_id,
    );

    $friendship = Friendship::where('initiator_id', $john_id)
        ->where('recipient_id', $jane_id)
        ->first();

    expect($friendship->status)->toBe('pending');
    expect($john->friends()->count())->toBe(0);
    expect($jane->friends()->count())->toBe(0);

    UserAddedFriend::fire(
        user_id: $jane_id,
        friend_id: $john_id,
    );

    $friendship->refresh();

    expect($friendship->status)->toBe('accepted');

    expect($john->friends()->count())->toBe(1);
    expect($jane->friends()->count())->toBe(1);
    expect(Friendship::all()->count())->toBe(1);
});

it('rejects friends', function () {
    $john_id = UserCreated::fire(
        name: 'John Doe', 
        email: 'john@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $john = User::find($john_id);

    $jane_id = UserCreated::fire(
        name: 'Jane Doe', 
        email: 'jane@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $jane = User::find($jane_id);

    UserAddedFriend::fire(
        user_id: $john_id,
        friend_id: $jane_id,
    );

    UserRejectedFriend::fire(
        user_id: $jane_id,
        friend_id: $john_id,
    );

    $friendship = $john->friendships()->first();

    expect($friendship->status)->toBe('rejected');

    expect($john->friends()->count())->toBe(0);
    expect($jane->friends()->count())->toBe(0);
    expect(Friendship::all()->count())->toBe(1);
});

it('ends an existing friendship', function () {
    $john_id = UserCreated::fire(
        name: 'John Doe', 
        email: 'john@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $john = User::find($john_id);

    $jane_id = UserCreated::fire(
        name: 'Jane Doe', 
        email: 'jane@doe.com', 
        password: bcrypt('password'),
    )->user_id;

    $jane = User::find($jane_id);

    UserAddedFriend::fire(
        user_id: $john_id,
        friend_id: $jane_id,
    );

    $friendship = Friendship::where('initiator_id', $john_id)
        ->where('recipient_id', $jane_id)
        ->first();

    expect($friendship->status)->toBe('pending');
    expect($john->friends()->count())->toBe(0);
    expect($jane->friends()->count())->toBe(0);

    UserAddedFriend::fire(
        user_id: $jane_id,
        friend_id: $john_id,
    );

    $friendship->refresh();

    expect($friendship->status)->toBe('accepted');

    expect($john->friends()->count())->toBe(1);
    expect($jane->friends()->count())->toBe(1);
    expect(Friendship::all()->count())->toBe(1);

    UserRejectedFriend::fire(
        user_id: $john_id,
        friend_id: $jane_id,
    );

    expect($john->friends()->count())->toBe(0);
    expect($jane->friends()->count())->toBe(0);
});
