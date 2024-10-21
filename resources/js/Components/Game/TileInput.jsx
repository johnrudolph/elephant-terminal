export default function TileInput({
    space,
    direction,
    onClick
}) {
    return (
        <button
            className="inline-flex items-center text-center mx-auto h-16"
            onClick={onClick}
        >
            {space} {direction}
        </button>
    );
}
