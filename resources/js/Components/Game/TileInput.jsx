import { motion } from 'framer-motion';
import { useRef, useState, useEffect } from "react";

export default function TileInput({
    space,
    direction,
    props,
    gameState,
}) {
    const [position, setPosition] = useState({ x: 0, y: 0 });
    const elementRef = useRef(null);
    const [initialOffset, setInitialOffset] = useState({ x: 0, y: 0 });

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

    const top_constraint = direction === 'up' ? -64 : 0;

    const left_constraint = direction === 'left' ? -64 : 0;

    const right_constraint = direction === 'right' ? 64 : 0;

    const bottom_constraint = direction === 'down' ? 64 : 0;

    const target_position = {
        left: { x: -64, y: 0 },
        right: { x: 64, y: 0 },
        up: { x: 0, y: -64 },
        down: { x: 0, y: 64 },
    }[direction];

    useEffect(() => {
        if (elementRef.current) {
            const rect = elementRef.current.getBoundingClientRect();
            setInitialOffset({ x: rect.left, y: rect.top });
        }
    }, []);

    const handleDragEnd = (event, info) => {
        console.log(info.offset.x, info.offset.y);

        if (
            info.offset.x - target_position.x < 30
            && info.offset.y - target_position.y < 30
        ) {
            console.log("Tile moved to target position");
        } else {
            console.log("Returning to original position");
            setPosition({ x: 0, y: 0 }); 
        }
    };

    return (
        <button
            className="inline-flex items-center text-center mx-auto h-16 animate-pulse"
            disabled={!enabled}
        >
            <motion.div
                ref={elementRef}
                className="w-12 h-12 z-10 rounded bg-red-300"
                drag
                dragConstraints={{
                    top: top_constraint,
                    left: left_constraint,
                    right: right_constraint,
                    bottom: bottom_constraint,
                }}
                onDragEnd={handleDragEnd}
                animate={position}
            />
        </button>
    );
}
