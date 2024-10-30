import TileInput from '@/Components/Game/TileInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import { Head, usePage, router } from '@inertiajs/react'
import { post } from '@/Utilities/http-client'
import Space from '@/Components/Game/Space';
import React, { useState, useEffect } from 'react';
import { affected_sliding_spaces } from '@/Utilities/board-logic'

export default function Game({ game }) {
  const props = usePage().props;

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

    const newMove = {
      spaces: affected_sliding_spaces(space, direction, gameState.board, props.player_id_string),
      direction: direction,
      type: 'tile',
    };

    setAnimationState((prevAnimationState) => ({
      ...prevAnimationState,
      queued_moves: [...prevAnimationState.queued_moves, newMove],
    }));

    post(
      route('games.play_tile', {game: props.game_id_string}),
      {
          game_id: props.game_id_string,
          space: space,
          direction: direction,
      },
      props.csrf_token
    )
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
          <div className="grid grid-cols-6 w-96 h-full justify-center mx-auto">
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="down" props={props} gameState={gameState} onClick={() => playTile(1, "down")}></TileInput>
            <TileInput space="2" direction="down" props={props} gameState={gameState} onClick={() => playTile(2, "down")}></TileInput>
            <TileInput space="3" direction="down" props={props} gameState={gameState} onClick={() => playTile(3, "down")}></TileInput>
            <TileInput space="4" direction="down" props={props} gameState={gameState} onClick={() => playTile(4, "down")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="right" props={props} gameState={gameState} onClick={() => playTile(1, "right")}></TileInput>
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
            <TileInput space="4" direction="left" props={props} gameState={gameState} onClick={() => playTile(4, "left")}></TileInput>
            <TileInput space="5" direction="right" props={props} gameState={gameState} onClick={() => playTile(5, "right")}></TileInput>
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
            <TileInput space="8" direction="left" props={props} gameState={gameState} onClick={() => playTile(8, "left")}></TileInput>
            <TileInput space="9" direction="right" props={props} gameState={gameState} onClick={() => playTile(9, "right")}></TileInput>
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
            <TileInput space="12" direction="left" props={props} gameState={gameState} onClick={() => playTile(12, "left")}></TileInput>
            <TileInput space="13" direction="right" props={props} gameState={gameState} onClick={() => playTile(1, "right")}></TileInput>
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
            <TileInput space="16" direction="left" props={props} gameState={gameState} onClick={() => playTile(16, "left")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="13" direction="up" props={props} gameState={gameState} onClick={() => playTile(13, "up")}></TileInput>
            <TileInput space="14" direction="up" props={props} gameState={gameState} onClick={() => playTile(14, "up")}></TileInput>
            <TileInput space="15" direction="up" props={props} gameState={gameState} onClick={() => playTile(15, "up")}></TileInput>
            <TileInput space="16" direction="up" props={props} gameState={gameState} onClick={() => playTile(16, "up")}></TileInput>
            <div className="h-full justify-center items-center"></div>
        </div>
    </AuthenticatedLayout>
  )
}

