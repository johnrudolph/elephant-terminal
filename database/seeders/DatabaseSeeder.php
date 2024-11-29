<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Events\UserCreated;
use App\Events\UserAddedFriend;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        UserCreated::fire(
            name: 'Bot 5000',
            email: 'bot@bot.bot',
            password: bcrypt('password'),  
        );

        $john_id =UserCreated::fire(
            name: 'John Rudolph Drexler',
            email: 'john@thunk.dev',
            password: bcrypt('password'),  
        )->user_id;

        $daniel_id = UserCreated::fire(
            name: 'Daniel Coulbourne',
            email: 'daniel@thunk.dev',
            password: bcrypt('password'),  
        )->user_id;

        $jacob_id = UserCreated::fire(
            name: 'Jacob Davis',
            email: 'jacob@thunk.dev',
            password: bcrypt('password'),  
        )->user_id;

        $lindsey_id = UserCreated::fire(
            name: 'Lindsey Evans',
            email: 'lindsey@thunk.dev',
            password: bcrypt('password'),  
        )->user_id;

        UserCreated::fire(
            name: 'Julie Drexler',
            email: 'julie@thunk.dev',
            password: bcrypt('password'),  
        );

        $michael_id = UserCreated::fire(
            name: 'Michael Johnson',
            email: 'michael@thunk.dev',
            password: bcrypt('password'),  
        )->user_id;

        UserCreated::fire(
            name: 'Ramona Johnson',
            email: 'ramona@thunk.dev',
            password: bcrypt('password'),  
        );

        UserAddedFriend::fire(
            user_id: $john_id,
            friend_id: $lindsey_id,
        );

        UserAddedFriend::fire(
            user_id: $lindsey_id,
            friend_id: $john_id,
        );

        UserAddedFriend::fire(
            user_id: $daniel_id,
            friend_id: $john_id,
        );

        UserAddedFriend::fire(
            user_id: $michael_id,
            friend_id: $john_id,
        );

        UserAddedFriend::fire(
            user_id: $john_id,
            friend_id: $jacob_id,
        );
    }
}
