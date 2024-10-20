<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'John Rudolph Drexler',
            'email' => 'john@thunk.dev',
            'password' => bcrypt('password'),
        ]);
    }
}
