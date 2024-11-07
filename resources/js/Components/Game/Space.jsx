import React, { useState, useEffect } from 'react';
import { motion, useAnimation } from 'framer-motion';

export default function Space({
    space,
    props,
    onClick,
    gameState,
    animationState,
}) {
    const [space_id, occupant] = space;
    const is_valid_move = gameState.valid_elephant_moves.includes(Number(space_id));
    const is_player_turn = gameState.current_player === props.player_id_string;
    const is_move_phase = gameState.phase === 'move';
    const game_is_active = gameState.status === 'active';
    const enabled = is_valid_move && is_player_turn && is_move_phase && game_is_active;

    const [animateTile, setAnimateTile] = useState(false);
    const controls = useAnimation();

    const animation_coordinates = {
        left: { x: 64, y: 0 },
        right: { x: -64, y: 0 },
        up: { x: 0, y: 64 },
        down: { x: 0, y: -64 },
    };

    const direction = (animationState.queued_moves && animationState.queued_moves.length > 0) 
        ? animationState.queued_moves[0].direction 
        : null;

    const startPosition = direction ? animation_coordinates[direction] : { x: 0, y: 0 };

    useEffect(() => {
        if (animationState.queued_moves && animationState.queued_moves.length > 0) {
            const first_move_to_animate = animationState.queued_moves[0];

            const should_animate_slide = first_move_to_animate.type === 'tile'
                && first_move_to_animate.spaces.map(i => Number(i.space_id)).includes(Number(space_id));

            setAnimateTile(should_animate_slide); 
        }
    }, [animationState]);

    // this should be animating, but isn't
    useEffect(() => {
        console.log(Number(space_id), animateTile);

        if (animateTile) {
            controls.start({
                x: 0,
                y: 0,
                transition: { duration: 0.8 } 
            }).then(() => {
                setAnimateTile(false);
            });
        }
    }, [animateTile]);

    return (
        <button
            className={`relative border z-90 border-black h-full flex justify-center items-center ${
                enabled ? 'bg-yellow-100 animate-pulse' : 'bg-gray-100'
            }`}
            disabled={!enabled}
            onClick={onClick}
        >
            {gameState.elephant_space === parseInt(space_id) && (
                <div className="absolute top-0 left-0 w-full h-full flex items-center justify-center z-10">
                    <p className="text-lg font-bold">E</p>
                </div>
            )}
            <motion.div
                initial={{ x: startPosition.x, y: startPosition.y }}
                animate={controls}
                style={{ position: 'absolute' }}
                className={`w-12 h-12 rounded ${
                    occupant === props.player_id_string && 'bg-sky-400'
                } ${
                    occupant === props.opponent_id_string && 'bg-red-300'
                }`}
            />
        </button>
    );
}
