export default function Space({
    space,
    props,
    onClick,
    gameState,
}) {
    const [space_id, occupant] = space;

    const is_valid_move = gameState.valid_elephant_moves.includes(Number(space_id));

    const is_player_turn = gameState.current_player === props.player_id_string;

    const is_move_phase = gameState.phase === 'move';

    const enabled = is_valid_move && is_player_turn && is_move_phase;

    return (
        <button
            className="border border-black h-full justify-center items-center"
            disabled={!enabled}
            onClick={onClick}
        >
            {occupant === props.player_id_string && <span className="h-full w-full bg-red-300 p-2">p1</span>}
            {occupant === props.opponent_id_string && <span className="h-full w-full bg-blue-300 p-2">p2</span>}
            {gameState.elephant_space === parseInt(space_id) && <p>E</p>}
        </button>
    );
}
