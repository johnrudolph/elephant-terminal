export default function TileInput({
    space,
    direction,
    onClick,
    props,
    gameState,
}) {
    const valid_slides_array = Object.values(gameState.valid_slides).map(({ space, direction }) => {
        return ['space', space, 'direction', direction];
      });

    const is_valid_slide = valid_slides_array.some(innerArray => 
        innerArray[1] === Number(space) && innerArray[3] === direction
      );

    const is_player_turn = gameState.current_player === props.player_id_string;

    const is_tile_phase = gameState.phase === 'tile';

    const enabled = is_valid_slide && is_player_turn && is_tile_phase;

    return (
        <button
            className="inline-flex items-center text-center mx-auto h-16"
            onClick={onClick}
            disabled={!enabled}
        >
            {enabled && space+" "+direction}
        </button>
    );
}
