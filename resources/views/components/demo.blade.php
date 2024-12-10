<div 
    x-data="{
        tiles: [],
        nextId: 1,
        init: 'true',
        valid_slides: [],
        elephant_space: 100,
        elephant_phase: false,
        tile_phase: false,
        current_player: 1,
        initialized: false,

        spaceToCoords(space) {
            const row = Math.floor((space - 1) / 4);
            const col = (space - 1) % 4;
            return {
                x: col * 61,  // 58px tile width + 2px gap
                y: row * 61   // 58px tile height + 2px gap
            };
        },

        playTile(direction, position, player_id) {
            const startPosition = {
                from_left:   { x: -60,     y: position * 60 },
                from_right:  { x: 240,     y: position * 60 },
                down:    { x: position * 60, y: -60 },
                up: { x: position * 60, y: 240 }
            };

            const targetSpace = {
                from_left:   position * 4 + 1,
                from_right:  (position * 4) + 4,
                down:    position + 1,
                up: position + 13
            }[direction];

            const shiftTilesFrom = (space, depth = 0) => {
                const existingTile = this.tiles.find(tile => tile.space === space);
                if (!existingTile) return;

                const currentRow = Math.floor((space - 1) / 4);
                const nextSpace = {
                    from_left: space + 1,
                    from_right: space - 1,
                    down: space + 4,
                    up: space - 4
                }[direction];
                
                // Check if the current space is valid for this row
                const isValidForRow = (spaceNum, row) => {
                    const spaceRow = Math.floor((spaceNum - 1) / 4);
                    return spaceRow === row;
                };

                // Only proceed if we're in the correct row for horizontal movements
                if (direction === 'from_left' || direction === 'from_right') {
                    if (!isValidForRow(space, currentRow)) {
                        return;
                    }
                }

                // Only recurse if the next space is valid
                if ((direction === 'from_left' || direction === 'from_right') && isValidForRow(nextSpace, currentRow)) {
                    shiftTilesFrom(nextSpace, depth + 1);
                } else if (direction === 'up' || direction === 'down') {
                    shiftTilesFrom(nextSpace, depth + 1);
                }

                if (depth === 3) {
                    const currentX = existingTile.x || 0;
                    const currentY = existingTile.y || 0;

                    const exitPosition = {
                        from_left:   { x: 240, y: currentY },
                        from_right:  { x: -60, y: currentY },
                        down:    { x: currentX, y: 240 },
                        up: { x: currentX, y: -60 }
                    }[direction];

                    if (exitPosition) {
                        const updatedTiles = this.tiles.map(tile => {
                            if (tile.id === existingTile.id) {
                                return {
                                    ...tile,
                                    x: exitPosition.x,
                                    y: exitPosition.y,
                                    opacity: 0,
                                    scale: 0.5
                                };
                            }
                            return tile;
                        });
                        this.tiles = updatedTiles;

                        setTimeout(() => {
                            this.tiles = this.tiles.filter(tile => tile.id !== existingTile.id);
                        }, 700);
                    }
                } else {
                    const nextCoords = this.spaceToCoords(nextSpace);
                    const updatedTiles = this.tiles.map(tile => {
                        if (tile.id === existingTile.id) {
                            return {
                                ...tile,
                                space: nextSpace,
                                x: nextCoords.x,
                                y: nextCoords.y
                            };
                        }
                        return tile;
                    });
                    this.tiles = updatedTiles;
                }
            };

            // Start the recursive shifting from the target space
            shiftTilesFrom(targetSpace);

            // Now place the new tile
            const tile_coords = this.spaceToCoords(targetSpace);
            const finalPosition = {
                x: tile_coords.x,
                y: tile_coords.y
            };

            const newTile = {
                id: this.nextId++,
                x: startPosition[direction].x,
                y: startPosition[direction].y,
                playerId: player_id,
                space: targetSpace
            };
            this.tiles.push(newTile);
            
            setTimeout(() => {
                const updatedTiles = this.tiles.map(tile => {
                    if (tile.id === newTile.id) {
                        return {
                            ...tile,
                            playerId: tile.playerId,
                            space: tile.space,
                            x: finalPosition.x,
                            y: finalPosition.y
                        };
                    }
                    return tile;
                });
                this.tiles = updatedTiles;
            }, 50);
        }
    }" 
    x-init="
        if( ! initialized) {
            initialized = true;
        
            const directions = ['from_left', 'from_right', 'up', 'down'];
        
            setInterval(() => {
                const randomDirection = directions[Math.floor(Math.random() * directions.length)];
                const randomPosition = Math.floor(Math.random() * 4);
                
                playTile(randomDirection, randomPosition, current_player);
                current_player = current_player === 1 ? 2 : 1;
            }, 1000);
        }
    "
    class="flex items-center justify-center flex-col space-y-8"
>
    {{-- Gameboard --}}
    <div class="inline-grid grid-cols-[auto_240px_auto] grid-rows-[auto_240px_auto] gap-1">
        <!-- Top area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <div>
                        <button 
                            class="w-[58px] h-8 animate-pulse flex items-center justify-center"
                            x-show="Object.values(valid_slides).some(slide => slide['space'] === i && slide['direction'] === 'down')"
                        >
                            ↓
                        </button>
                        <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === i && slide['direction'] === 'down')">
                            <div class="w-[58px] h-8"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Left area -->
        <div class="col-start-1 w-8">
            <div class="grid grid-rows-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4" >
                    <div>
                        <button 
                            class="h-[58px] w-8 animate-pulse flex items-center justify-center"
                            x-show="Object.values(valid_slides).some(slide => slide['space'] === 1 + (i - 1) * 4 && slide['direction'] === 'right')"
                        >
                            →
                        </button>
                        <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === 1 + (i - 1) * 4 && slide['direction'] === 'right')">
                            <div class="h-[58px] w-8"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Main grid -->
        <div class="relative h-[240px] w-[240px] grid grid-cols-4 grid-rows-4 gap-1">
            <!-- Grid spaces -->
            <template x-for="i in 16">
                <div class="relative">
                    <div class="absolute w-58 h-58 inset-0 bg-gray-100 dark:bg-zinc-700 rounded-lg"></div>
                </div>
            </template>
            
            <!-- Tiles -->
            <template x-for="tile in tiles" :key="tile.id">
                <div 
                    class="absolute w-[58px] h-[58px] rounded-lg transition-all duration-700 ease-in-out"
                    :class="tile.playerId === 1 ? 'bg-pink' : 'bg-forest-green'"
                    :style="`
                        transform: translate(${tile.x}px, ${tile.y}px) scale(${tile.scale || 1});
                        opacity: ${tile.opacity === undefined ? 1 : tile.opacity};
                    `"
                ></div>
            </template>
        </div>

        <!-- Right area -->
        <div class="col-start-3 w-8">
            <div class="grid grid-rows-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <div>
                        <button 
                            class="h-[58px] w-8 animate-pulse rounded-lg flex items-center justify-center"
                            x-show="Object.values(valid_slides).some(slide => slide['space'] === i * 4 && slide['direction'] === 'left')"
                        >
                            ←
                        </button>
                        <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === i * 4 && slide['direction'] === 'left')">
                            <div class="h-[58px] w-8"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Bottom area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <div>
                        <button 
                            class="w-[58px] h-8 animate-pulse flex items-center justify-center"
                            x-show="Object.values(valid_slides).some(slide => slide['space'] === i +12 && slide['direction'] === 'up')"
                        >
                            ↑
                        </button>
                        <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === i + 12 && slide['direction'] === 'up')">
                            <div class="w-[58px] h-8"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
