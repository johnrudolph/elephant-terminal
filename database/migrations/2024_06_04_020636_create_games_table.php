<?php

use App\Models\Player;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('status')->enum('created', 'active', 'complete')->default('created');
            $table->json('board');
            $table->json('valid_slides');
            $table->json('valid_elephant_moves');
            $table->integer('elephant_space');
            $table->string('phase')->enum('elephant', 'tile');
            $table->foreignIdFor(Player::class, 'current_player_id')->nullable();
            $table->json('victor_ids')->nullable();
            $table->json('winning_spaces')->nullable();
            $table->boolean('is_ranked')->default(false);
            $table->boolean('is_friends_only')->default(false);
            $table->timestamps();
        });
    }
};
