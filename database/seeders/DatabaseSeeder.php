<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Events\UserCreated;
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

        UserCreated::fire(
            name: 'John Rudolph Drexler',
            email: 'john@thunk.dev',
            password: bcrypt('password'),  
        );
    }
}
