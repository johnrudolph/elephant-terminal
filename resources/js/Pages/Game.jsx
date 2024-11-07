import TileInput from '@/Components/Game/TileInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import { Head, usePage, router } from '@inertiajs/react'
import { post } from '@/Utilities/http-client'
import Space from '@/Components/Game/Space';
import React, { useState, useEffect } from 'react';
import { affected_sliding_spaces } from '@/Utilities/board-logic'

export default function Game({ game }) {
  const props = usePage().props;

  const user_player = props.players.find(player => player.is_user === true);

  const [gameState, setGameState] = useState({
    board: Object.entries(props.game.board),
    phase: props.game.phase,
    current_player: props.current_player_id_string,
    elephant_space: props.game.elephant_space,
    valid_slides: props.game.valid_slides,
    valid_elephant_moves: props.game.valid_elephant_moves,
    status: props.game.status,
  });

  const moves = Object.entries(props.moves)[0][1].map((move) => {
    let spaces = move.type === 'elephant'
      ? [move.elephant_after]
      : affected_sliding_spaces(move.initial_slide['space'], move.initial_slide['direction'], gameState.board, props.player_id_string)

    return {
      spaces: spaces,
      direction: move.direction,
      type: move.type,
    };
  });

  const [animationState, setAnimationState] = useState({
    previous_moves: moves,
    queued_moves: [], 
  });

  const queueMoves = (moves) => {
      setAnimationState(prev => ({
          ...prev,
          queued_moves: moves.map((move, index) => ({
              ...move,
              id: `move-${Date.now()}-${index}` // Add unique id to each move
          }))
      }));
  }; 

  Echo.private(`games.${props.game_id_string}`)
    .listen('PlayerPlayedTileBroadcast', (e) => {
      refresh_game(e.move_id);
    });

  Echo.private(`games.${props.game_id_string}`)
    .listen('PlayerMovedElephantBroadcast', (e) => {
      refresh_game(e.move_id);
    });

  const refresh_game = (move_id) => {
    router.get(
      route('games.show', {game: props.game_id_string}),
    );
  }

  const playTile = (space, direction) => {
    const updatedBoard = [...gameState.board];
    updatedBoard[space - 1] = [space, props.player_id_string]; 

    setGameState((prevState) => ({
      ...prevState,
      board: updatedBoard,
      phase: 'move',
    }));

    const affectedSpaces = affected_sliding_spaces(space, direction, gameState.board, props.player_id_string);
    
    // Create a single move with all affected spaces
    const newMove = {
        id: `move-${Date.now()}`,  // Add unique identifier
        spaces: affectedSpaces,
        direction: direction,
        type: 'tile',
    };

    // Replace queued moves instead of adding to them
    setAnimationState({
        previous_moves: [],  // Clear previous moves
        queued_moves: [newMove]  // Set as single queued move
    });

    // Uncomment your post request here
  }

  const moveElephant = (space) => {
    const [space_id, occupant] = space;

    setGameState((prevState) => ({
      ...prevState,
      phase: 'tile',
      current_player: props.opponent_id_string,
      elephant_space: Number(space_id),
    }));

    post(
      route('games.move_elephant', {game: props.game_id_string}),
      {
          game_id: props.game_id_string,
          space: space_id,
      },
      props.csrf_token
    )
  }

  return (
    <AuthenticatedLayout
        header={
            <h2 className="text-xl font-semibold leading-tight text-gray-800">
                Game ({game.code})
            </h2>
        }
    >
        <Head title="Game" />
          <p>{user_player.victory_shape}</p>
          <div className="grid grid-cols-6 w-96 h-full justify-center mx-auto">
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="down" props={props} gameState={gameState} onTileMoved={() => playTile(1, "down")}></TileInput>
            <TileInput space="2" direction="down" props={props} gameState={gameState} onTileMoved={() => playTile(2, "down")}></TileInput>
            <TileInput space="3" direction="down" props={props} gameState={gameState} onTileMoved={() => playTile(3, "down")}></TileInput>
            <TileInput space="4" direction="down" props={props} gameState={gameState} onTileMoved={() => playTile(4, "down")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="right" props={props} gameState={gameState} onTileMoved={() => playTile(1, "right")}></TileInput>
            {gameState.board.slice(0, 4).map((space, index) => (
              <Space 
                key={space} 
                space={space} 
                animationState = {animationState} 
                gameState={gameState} 
                onClick={() => moveElephant(space)} 
                props={props} 
              ></Space>
            ))}
            <TileInput space="4" direction="left" props={props} gameState={gameState} onTileMoved={() => playTile(4, "left")}></TileInput>
            <TileInput space="5" direction="right" props={props} gameState={gameState} onTileMoved={() => playTile(5, "right")}></TileInput>
            {gameState.board.slice(4, 8).map((space, index) => (
              <Space 
                key={space} 
                space={space} 
                animationState = {animationState} 
                gameState={gameState} 
                onClick={() => moveElephant(space)} 
                props={props} 
              ></Space>
            ))}
            <TileInput space="8" direction="left" props={props} gameState={gameState} onTileMoved={() => playTile(8, "left")}></TileInput>
            <TileInput space="9" direction="right" props={props} gameState={gameState} onTileMoved={() => playTile(9, "right")}></TileInput>
            {gameState.board.slice(8, 12).map((space, index) => (
              <Space 
                key={space} 
                space={space} 
                animationState = {animationState} 
                gameState={gameState} 
                onClick={() => moveElephant(space)} 
                props={props} 
              ></Space>
            ))}
            <TileInput space="12" direction="left" props={props} gameState={gameState} onTileMoved={() => playTile(12, "left")}></TileInput>
            <TileInput space="13" direction="right" props={props} gameState={gameState} onTileMoved={() => playTile(1, "right")}></TileInput>
            {gameState.board.slice(12, 16).map((space, index) => (
              <Space 
                key={space} 
                space={space} 
                animationState = {animationState} 
                gameState={gameState} 
                onClick={() => moveElephant(space)} 
                props={props} 
              ></Space>
            ))}
            <TileInput space="16" direction="left" props={props} gameState={gameState} onTileMoved={() => playTile(16, "left")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="13" direction="up" props={props} gameState={gameState} onTileMoved={() => playTile(13, "up")}></TileInput>
            <TileInput space="14" direction="up" props={props} gameState={gameState} onTileMoved={() => playTile(14, "up")}></TileInput>
            <TileInput space="15" direction="up" props={props} gameState={gameState} onTileMoved={() => playTile(15, "up")}></TileInput>
            <TileInput space="16" direction="up" props={props} gameState={gameState} onTileMoved={() => playTile(16, "up")}></TileInput>
            <div className="h-full justify-center items-center"></div>
        </div>
    </AuthenticatedLayout>
  )
}

