<div x-data="{
        tiles: [],
        nextId: 1,
        init: 'true',
        is_player_turn: {{ $this->is_player_turn ? 'true' : 'false' }},
        phase: '{{ $this->game->phase }}',
        game_status: '{{ $this->game->status }}',
        validMoves: @json($this->game->valid_elephant_moves),
        get tile_phase() {
            return this.is_player_turn && this.phase === 'tile' && this.game_status === 'active';
        },
        get elephant_phase() {
            return this.is_player_turn && this.phase === 'move' && this.game_status === 'active';
        },

        spaceToCoords(space) {
            console.log('Converting space:', space);  // Add this debug line
            const row = Math.floor((space - 1) / 4);
            const col = (space - 1) % 4;
            const coords = {
                x: col * 60,
                y: row * 60
            };
            console.log('Calculated coords:', coords);  // Add this debug line
            return coords;
        },

        initializeTilesAndElephant() {
            @foreach($this->tiles as $space => $playerId)
                const coords = this.spaceToCoords({{ $space }});
                this.tiles.push({
                    id: this.nextId++,
                    x: coords.x,
                    y: coords.y,
                    playerId: {{ $playerId }},
                    space: {{ $space }}
                });
            @endforeach

            const elephantCoords = this.spaceToCoords({{ $elephant_space }});
            this.$refs.elephant.style.transform = `translate(${elephantCoords.x}px, ${elephantCoords.y}px)`;
            
            setTimeout(() => {
                this.init = false;
            }, 100);
        },

        playTile(direction, position) {
            this.phase = 'move';

            const startPosition = {
                from_left:   { x: -60,     y: position * 60 },
                from_right:  { x: 240,     y: position * 60 },
                down:    { x: position * 60, y: -60 },
                up: { x: position * 60, y: 240 }
            };

            // Calculate target space based on direction and position
            const targetSpace = {
                from_left:   position * 4 + 1,         // First column of each row
                from_right:  (position * 4) + 4,       // Last column of each row
                down:    position + 1,             // First row
                up: position + 13             // Last row
            }[direction];

            const coords = this.spaceToCoords(targetSpace);
            const finalPosition = {
                x: coords.x,
                y: coords.y
            };

            const newTile = {
                id: this.nextId++,
                x: startPosition[direction].x,
                y: startPosition[direction].y,
                playerId: {{ $this->player->id }}, 
                space: targetSpace
            };
            this.tiles.push(newTile);
            
            setTimeout(() => {
                const updatedTiles = this.tiles.map(tile => {
                    if (tile.id === newTile.id) {
                        return {
                            id: tile.id,
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
        },

        moveElephant(space) {
            console.log('Moving to space:', space);  // Add this debug line
            const coords = this.spaceToCoords(space);
            console.log('Moving to coords:', coords);  // Add this debug line
            this.$refs.elephant.style.transform = `translate(${coords.x}px, ${coords.y}px)`;
            
            this.phase = 'tile';
        }
    }" 
    x-init="initializeTilesAndElephant()"
    class="min-h-screen flex items-center justify-center"
>
    <div class="inline-grid grid-cols-[auto_240px_auto] grid-rows-[auto_240px_auto] gap-1">
        <!-- Top area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <button 
                        @click="playTile('down', i-1); $wire.playTile('down', i)"
                        class="w-[58px] bg-gray-50 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-600 dark:text-zinc-400 flex items-center justify-center"
                    >
                        ↓
                    </button>
                </template>
            </div>
        </div>

        <!-- Left area -->
        <div class="col-start-1 w-8">
            <div class="grid grid-rows-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4" >
                    <button 
                        @click="playTile('from_left', i-1); $wire.playTile('right', i)"
                        class="h-[58px] bg-gray-50 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-600 dark:text-zinc-400 flex items-center justify-center"
                    >
                        →
                    </button>
                </template>
            </div>
        </div>

        <!-- Main grid -->
        <div class="relative h-[240px] w-[240px] dark:bg-zinc-800 grid grid-cols-4 grid-rows-4 gap-1">
            <!-- Grid spaces -->
            <template x-for="i in 16">
                <div class="relative">
                    <button 
                        x-show="elephant_phase && validMoves.includes(i)"
                        @click="moveElephant(i); $wire.moveElephant(i)" 
                        class="absolute inset-0 bg-slate-100 opacity-40 animate-pulse rounded-lg z-20"
                    ></button>
                    <div 
                        class="absolute inset-0 bg-gray-100 dark:bg-zinc-700 rounded-lg"
                        x-show="!elephant_phase || !validMoves.includes(i)"
                    ></div>
                </div>
            </template>
            
            <!-- Tiles -->
            <template x-for="tile in tiles" :key="tile.id">
                <div 
                    class="absolute w-[58px] h-[58px] bg-blue-500 rounded-lg transition-all duration-700 ease-in-out"
                    :style="`transform: translate(${tile.x}px, ${tile.y}px)`"
                ></div>
            </template>

            <!-- Elephant -->
            <div 
                x-ref="elephant"
                class="absolute w-[58px] h-[58px]"
                :class="{ 'transition-all duration-700 ease-in-out': !init }"
            >
                <x-svg.elephant class="w-11 h-11 mx-auto mt-2 z-90 dark:text-zinc-200"/>
            </div>
        </div>

        <!-- Right area -->
        <div class="col-start-3 w-8">
            <div class="grid grid-rows-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <button 
                        @click="playTile('from_right', i-1); $wire.playTile('left', i)"
                        class="h-[58px] rounded-lg bg-gray-50 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-600 dark:text-zinc-400 flex items-center justify-center"
                    >
                        ←
                    </button>
                </template>
            </div>
        </div>

        <!-- Bottom area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <button 
                        @click="playTile('up', i-1); $wire.playTile('up', i)"
                        class="w-[58px] bg-gray-50 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-600 dark:text-zinc-400 flex items-center justify-center"
                    >
                        ↑
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
