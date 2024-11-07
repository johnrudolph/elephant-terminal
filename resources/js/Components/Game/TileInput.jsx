import { motion } from 'framer-motion';
import { useRef, useState, useEffect } from "react";

export default function TileInput({
    space,
    direction,
    props,
    gameState,
    onTileMoved,
}) {
    const valid_slides_array = Object.values(gameState.valid_slides).map(({ space, direction }) => {
        return ['space', space, 'direction', direction];
      });

    const is_valid_slide = valid_slides_array.some(innerArray => 
        innerArray[1] === Number(space) && innerArray[3] === direction
      );

    const is_player_turn = gameState.current_player === props.player_id_string;
    const is_tile_phase = gameState.phase === 'tile';
    const game_is_active = gameState.status === 'active';
    const enabled = is_valid_slide && is_player_turn && is_tile_phase && game_is_active;

    const target_position = {
        left: { x: -64, y: 0 },
        right: { x: 64, y: 0 },
        up: { x: 0, y: -64 },
        down: { x: 0, y: 64 },
    }[direction];

    const handleDragEnd = (event, info) => {
        if (
            info.offset.x - target_position.x < 30
            && info.offset.y - target_position.y < 30
        ) {
            onTileMoved(space, direction);
        } else {
            // @todo: return to original position
        }
    };

    return (
        <div className="inline-flex items-center text-center mx-auto h-16 animate-pulse">
            {enabled && (<motion.div
                className="w-12 h-12 z-10 rounded bg-sky-400"
                drag
                dragConstraints={{
                    top: direction === 'up' ? -64 : 0,
                    left: direction === 'left' ? -64 : 0,
                    right: direction === 'right' ? 64 : 0,
                    bottom: direction === 'down' ? 64 : 0,
                }}
                onDragEnd={handleDragEnd}
                animate={{ x: 0, y: 0 }}
            />)}
        </div>
    );
}
