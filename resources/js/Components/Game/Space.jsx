export default function Space({
    space,
    props,
    onClick,
    disabled,
}) {
    const [space_id, occupant] = space;

    return (
        <button
            className="border border-black h-full justify-center items-center"
            disabled={disabled}
            onClick={onClick}
        >
            {occupant === props.player_id_string && <span className="h-full w-full bg-red-300 p-2">p1</span>}
            {occupant === props.opponent_id_string && <span className="h-full w-full bg-blue-300 p-2">p2</span>}
            {props.game.elephant_space === parseInt(space_id) && <p>E</p>}
        </button>
    );
}
