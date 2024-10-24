<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games', 'id');
            $table->foreignId('player_id')->constrained('players', 'id');
            $table->string('type')->enum('tile', 'elephant');
            $table->json('initial_slide')->nullable();
            $table->json('board_before');
            $table->json('board_after');
            $table->integer('elephant_before');
            $table->integer('elephant_after');
            $table->json('bot_move_scores')->nullable();
            $table->timestamps();
        });
    }
};
