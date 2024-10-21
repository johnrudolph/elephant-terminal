<?php

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Game::class);
            $table->foreignIdFor(User::class);
            $table->integer('hand')->default(8);
            $table->boolean('is_host');
            $table->boolean('is_bot');
            $table->timestamps();
        });
    }
};
