import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

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

    let should_animate_slide = false;

    if (animationState.queued_moves && animationState.queued_moves.length > 0) {
        let first_move_to_animate = animationState.queued_moves[0];

        should_animate_slide = first_move_to_animate.type === 'tile'
            && first_move_to_animate.spaces.map(i => i.space_id).includes(Number(space_id));
    } 

    const [animateTile, setAnimateTile] = useState(should_animate_slide);

    // useEffect(() => {
    //     if (animateTile) {
    //         runAnimation();
    //     }
    // }, [animateTile]);

    // const runAnimation = () => {
    //   setAnimateTile(false); 
    // };

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
                animate={animateTile ? { x:-20, y:-20 } : { x:0, y:0 }} 
                transition={{ duration: 0.8 }}
                className={`w-12 h-12 rounded ${
                    occupant === props.player_id_string && 'bg-sky-400'
                } ${
                    occupant === props.opponent_id_string && 'bg-red-300'
                }`}
            />
        </button>
    );
}
