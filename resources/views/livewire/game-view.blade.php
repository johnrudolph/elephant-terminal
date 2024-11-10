<div wire:ignore x-data="{
        tiles: [],
        elephant_space: @entangle('elephant_space'),
        nextId: 1,
        init: 'true',
        player_hand: @entangle('player_hand'),
        opponent_hand: @entangle('opponent_hand'),
        is_player_turn: @entangle('is_player_turn'),
        phase: @entangle('phase'),
        game_status: @entangle('game_status'),
        valid_elephant_moves: @entangle('valid_elephant_moves'),
        valid_slides: @entangle('valid_slides'),
        get tile_phase() {
            return this.is_player_turn && this.phase === 'tile' && this.game_status === 'active';
        },
        get elephant_phase() {
            return this.is_player_turn && this.phase === 'move' && this.game_status === 'active';
        },

        spaceToCoords(space) {
            const row = Math.floor((space - 1) / 4);
            const col = (space - 1) % 4;
            return {
                x: col * 60 + col,
                y: row * 60 + row
            };
        },

        initializeTilesAndElephant() {
            @foreach($this->tiles as $space => $playerId)
                this.tiles.push({
                    id: this.nextId++,
                    x: this.spaceToCoords({{ $space }}).x,
                    y: this.spaceToCoords({{ $space }}).y,
                    playerId: {{ $playerId }},
                    space: {{ $space }}
                });
            @endforeach

            const elephant_coords = this.spaceToCoords(this.elephant_space);  
            this.$refs.elephant.style.transform = `translate(${elephant_coords.x}px, ${elephant_coords.y}px)`;
            
            setTimeout(() => {
                this.init = false;
            }, 100);
        },

        moveElephant(space) {
            this.elephant_space = space;
            const coords = this.spaceToCoords(space);
            this.$refs.elephant.style.transform = `translate(${coords.x}px, ${coords.y}px)`;
            
            this.phase = 'tile';
            this.is_player_turn = false;
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

            const tile_coords = this.spaceToCoords(targetSpace);
            const finalPosition = {
                x: tile_coords.x,
                y: tile_coords.y
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
        }
    }" 
    x-init="
        initializeTilesAndElephant();
        
        $watch('elephant_space', (value) => {
            console.log('watch_elephant_space');
            const coords = $data.spaceToCoords(value);  // Use $data to access component methods
            $refs.elephant.style.transform = `translate(${coords.x}px, ${coords.y}px)`;
        })

        $watch('phase', (value) => {
            console.log('watch_phase', value);
            this.phase = value;
        })

        $watch('game_status', (value) => {
            console.log('watch_game_status', value);
            this.game_status = value;
        })

        $watch('valid_elephant_moves', (value) => {
            console.log('watch_valid_elephant_moves', value);
            this.valid_elephant_moves = value;
        })

        $watch('valid_slides', (value) => {
            console.log('watch_valid_slides', value);
            this.valid_slides = value;
        })

        $watch('is_player_turn', (value) => {
            console.log('watch_is_player_turn', value);
            this.is_player_turn = value;
        })

        $watch('player_hand', (value) => {
            console.log('watch_player_hand', value);
            this.player_hand = value;
        })

        $watch('opponent_hand', (value) => {
            console.log('watch_opponent_hand', value);
            this.opponent_hand = value;
        })
    "
    class="min-h-screen flex items-center justify-center flex-col space-y-8"
>
    {{-- Scoreboard --}}
    <div class="flex flex-col items-center justify-center space-y-4 w-[300px]" x-data="{ player_hand: {{ $this->player->hand }}, opponent_hand: {{ $this->opponent->hand }} }">
        <x-player-card :player="$this->player" :player_color="'bg-blue-500'" x-bind:hand="player_hand" />
        <x-player-card :player="$this->opponent" :player_color="'bg-red-500'" x-bind:hand="opponent_hand" />
    </div>

    {{-- Gameboard --}}
    <div class="inline-grid grid-cols-[auto_240px_auto] grid-rows-[auto_240px_auto] gap-1">
        <!-- Top area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <button 
                        @click="playTile('down', i-1); $wire.playTile('down', i)"
                        class="w-[58px] animate-pulse flex items-center justify-center"
                        x-show="Object.values(valid_slides).some(slide => slide['space'] === i && slide['direction'] === 'down')"
                    >
                        ↓
                    </button>
                    <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === i && slide['direction'] === 'down')">
                        <div class="h-[58px]"></div>
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
                            @click="playTile('from_left', i-1); $wire.playTile('right', i)"
                            class="h-[58px] animate-pulse flex items-center justify-center"
                            x-show="Object.values(valid_slides).some(slide => slide['space'] === 1 + (i - 1) * 4 && slide['direction'] === 'right')"
                        >
                            →
                        </button>
                        <div x-show="!Object.values(valid_slides).some(slide => slide['space'] === 1 + (i - 1) * 4 && slide['direction'] === 'right')">
                            <div class="h-[58px]"></div>
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
                    <button 
                        x-show="elephant_phase && valid_elephant_moves.includes(i)"
                        @click="moveElephant(i); $wire.moveElephant(i)" 
                        class="absolute inset-0 bg-slate-900 opacity-20 animate-pulse rounded-lg z-20"
                    ></button>
                    <div 
                        class="absolute inset-0 bg-gray-100 rounded-lg"
                        x-show="!elephant_phase || !valid_elephant_moves.includes(i)"
                    ></div>
                </div>
            </template>
            
            <!-- Tiles -->
            <template x-for="tile in tiles" :key="tile.id">
                <div 
                    class="absolute w-[58px] h-[58px] rounded-lg transition-all duration-700 ease-in-out"
                    :class="tile.playerId === {{ $this->player->id }} ? 'bg-blue-500' : 'bg-red-500'"
                    :style="`transform: translate(${tile.x}px, ${tile.y}px)`"
                ></div>
            </template>

            <!-- Elephant -->
            <div 
                x-ref="elephant"
                class="absolute w-[58px] h-[58px]"
                :class="{ 'transition-all duration-700 ease-in-out': !init }"
            >
                <x-svg.elephant class="w-11 h-11 mx-auto mt-2 z-90"/>
            </div>
        </div>

        <!-- Right area -->
        <div class="col-start-3 w-8">
            <div class="grid grid-rows-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <button 
                        @click="playTile('from_right', i-1); $wire.playTile('left', i)"
                        class="h-[58px] animate-pulse rounded-lg flex items-center justify-center"
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
                        class="w-[58px] animate-pulse flex items-center justify-center"
                    >
                        ↑
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
