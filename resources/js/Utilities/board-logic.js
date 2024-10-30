const affected_sliding_spaces = (space_id, direction, board, new_tile_player_id_string) => {
    let spaces_array = [
        {
            space_id: space_id,
            direction: direction,
            occupant: new_tile_player_id_string,
        }
    ];

    const sliding_positions = slide_positions(space_id, direction);

    const space_1_occupant = board[sliding_positions[0] - 1][1];
    const space_2_occupant = board[sliding_positions[1] - 1][1];
    const space_3_occupant = board[sliding_positions[2] - 1][1];
    const space_4_occupant = board[sliding_positions[3] - 1][1];

    if (space_1_occupant && space_2_occupant && space_3_occupant) {
        spaces_array.push({
            space_id: sliding_positions[3],
            direction: direction,
            occupant: space_3_occupant
        });
    }

    if (space_1_occupant && space_2_occupant) {
        spaces_array.push({
            space_id: sliding_positions[2],
            direction: direction,
            occupant: space_2_occupant
        });
    }

    if (space_1_occupant) {
        spaces_array.push({
            space_id: sliding_positions[1],
            direction: direction,
            occupant: space_1_occupant
        });
    }

    return spaces_array;
}

const slide_positions = (space_id, direction) => {
    const sliding_positions = {
        1: {
            'down': [1, 5, 9, 13],
            'right': [1, 2, 3, 4],
        },
        2: {
            'down': [2, 6, 10, 14],
        },
        3: {
            'down': [3, 7, 11, 15],
        },
        4: {
            'down': [4, 8, 12, 16],
            'left': [4, 3, 2, 1],
        },
        5: {
            'right': [5, 6, 7, 8],
        },
        8: {
            'left': [8, 7, 6, 5],
        },
        9: {
            'right': [9, 10, 11, 12],
        },
        12: {
            'left': [12, 11, 10, 9],
        },
        13: {
            'right': [13, 14, 15, 16],
            'up': [13, 9, 5, 1],
        },
        14: {
            'up': [14, 10, 6, 2],
        },
        15: {
            'up': [15, 11, 7, 3],
        },
        16: {
            'up': [16, 12, 8, 4],
            'left': [16, 15, 14, 13],
        },
    };

    return sliding_positions[space_id]?.[direction] || [];
}


export {
    slide_positions,
    affected_sliding_spaces,
}