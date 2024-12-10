<div 
    wire:ignore 
    wire:poll.5000ms="check_for_moves"
    x-data="{
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
        animating: false,
        get tile_phase() {
            return !this.animating && this.is_player_turn && this.phase === 'tile' && this.game_status === 'active';
        },
        get elephant_phase() {
            return !this.animating && this.is_player_turn && this.phase === 'move' && this.game_status === 'active';
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
            this.animating = true;
            this.elephant_space = space;
            const coords = this.spaceToCoords(space);
            this.$refs.elephant.style.transform = `translate(${coords.x}px, ${coords.y}px)`;
            
            setTimeout(() => {
                this.phase = 'tile';
                this.animating = false;
                if(this.opponent_hand > 0) {
                    this.is_player_turn = false;
                }
            }, 700);
        },

        playTile(direction, position, player_id) {
            this.phase = 'move';

            if (player_id === {{ $this->player->id }}) {
                this.player_hand--;
            } else {
                this.opponent_hand--;
            }

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

                        if(existingTile.playerId === {{ $this->player->id }}) {
                            this.player_hand++;
                        } else {
                            this.opponent_hand++;
                        }

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

        $watch('phase', function(value) {
            this.phase = value;
        });

        $watch('game_status', function(value) {
            this.game_status = value;
        });

        $watch('valid_elephant_moves', function(value) {
            this.valid_elephant_moves = value;
        });

        $watch('valid_slides', function(value) {
            this.valid_slides = value;
        });

        $watch('is_player_turn', function(value) {
            this.is_player_turn = value;
        });

        $watch('player_hand', function(value) {
            this.player_hand = value;
        });

        $watch('opponent_hand', function(value) {
            this.opponent_hand = value;
        });

        Livewire.on('opponent-played-tile', async function(data) {
            $data.animating = true;
            playTile(data[0].direction, data[0].position, data[0].player_id);
        });

        Livewire.on('opponent-moved-elephant', async function(data) {
            $data.animating = true;
            const coords = $data.spaceToCoords(data[0].position);
            $refs.elephant.style.transform = `translate(${coords.x}px, ${coords.y}px)`;
            setTimeout(() => {
                $data.animating = false;
            }, 700);
        });
    "
    class="flex items-center justify-center flex-col space-y-8"
>
    {{-- player info --}}
    <div class="flex flex-col items-center justify-center space-y-4 w-[300px]">
        <flux:card class="w-full" >
            <div class="flex flex-row justify-between items-center text-zinc-800 dark:text-zinc-200" :class="{ 'animate-pulse': is_player_turn && game_status === 'active' }">
                <div class="flex flex-col items-start w-full">
                    <flux:heading class="text-left w-full">
                        {{ $this->player->user->name }}
                        <flux:badge color="gray" size="sm" variant="outline" class="ml-1">{{ $this->player->user->rating }}</flux:badge>
                    </flux:heading>
                    <div class="mt-2 flex flex-row space-x-2 items-center">
                        <div class="bg-beige w-8 h-8 rounded-lg flex items-center justify-center">
                            <p class="font-bold text-white" x-text="player_hand"></p>
                        </div>
                        <p class="text-xs">remaining</p>
                    </div>
                </div>
                <x-dynamic-component 
                    :component="'svg.' . $this->player->victory_shape"
                    class="w-14 h-14"
                />
            </div>
        </flux:card>

        <flux:card class="w-full" >
            <div class="flex flex-row justify-between items-center text-zinc-800 dark:text-zinc-200" :class="{ 'animate-pulse': !is_player_turn && game_status === 'active' }">
            <div class="flex flex-col items-start w-full space-y-2">
                <flux:heading class="text-left w-full">
                    {{ $this->opponent->user->name }}
                    <flux:badge color="gray" size="sm" variant="outline" class="ml-1">{{ $this->opponent->user->rating }}</flux:badge>
                </flux:heading>
                @if($this->opponent_is_friend === 'request_incoming')
                    <flux:button variant="ghost" inset size="xs" wire:click="sendFriendRequest">Confirm friend request</flux:button>
                @elseif($this->opponent_is_friend === 'request_outgoing')
                    <flux:badge size="sm" color="gray">Request sent</flux:badge>
                @elseif($this->opponent_is_friend === 'not_friends')
                    <flux:button variant="ghost" inset size="xs" wire:click="sendFriendRequest">Send friend request</flux:button>
                @elseif($this->opponent_is_friend === 'friends')
                    <flux:badge size="sm" color="green">Friends</flux:badge>
                @endif
                <div class="flex flex-row space-x-2 mt-2 items-center">
                    <div class="bg-forest-green w-8 h-8 rounded-lg flex items-center justify-center">
                        <p class="font-bold text-white" x-text="opponent_hand"></p>
                    </div>
                    <p class="text-xs">remaining</p>
                </div>
            </div>
            <x-dynamic-component 
                :component="'svg.' . $this->opponent->victory_shape"
                class="w-14 h-14"
            />
            </div>
        </flux:card>
    </div>

    {{-- debug info --}}
    <div class="fixed top-4 right-4 bg-black/50 text-white p-2 rounded space-y-1">
        <div>Phase: <span x-text="phase"></span></div>
        <div>Animating: <span x-text="animating"></span></div>
        <div>Is Player Turn: <span x-text="is_player_turn"></span></div>
        <div>Game Status: <span x-text="game_status"></span></div>
    </div>

    {{-- Gameboard --}}
    <div class="inline-grid grid-cols-[auto_240px_auto] grid-rows-[auto_240px_auto] gap-1">
        <!-- Top area -->
        <div class="col-start-2 h-8">
            <div class="grid grid-cols-4 gap-1" x-show="tile_phase">
                <template x-for="i in 4">
                    <div>
                        <button 
                            @click="playTile('down', i-1, {{ $this->player->id }}); $wire.playTile('down', i)"
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
                            @click="playTile('from_left', i-1, {{ $this->player->id }}); $wire.playTile('right', i)"
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
                    <button 
                        x-show="elephant_phase && valid_elephant_moves.includes(i) && game_status === 'active' && is_player_turn"
                        @click="moveElephant(i); $wire.moveElephant(i)" 
                        class="absolute inset-0 bg-slate-400 opacity-20 animate-pulse rounded-lg z-20"
                    ></button>
                    <div 
                        class="absolute inset-0 bg-gray-100 dark:opacity-20 dark:bg-zinc-700 rounded-lg"
                        x-show="!elephant_phase || !valid_elephant_moves.includes(i)"
                    ></div>
                </div>
            </template>
            
            <!-- Tiles -->
            <template x-for="tile in tiles" :key="tile.id">
                <div 
                    class="absolute w-[58px] h-[58px] rounded-lg transition-all duration-700 ease-in-out"
                    :class="tile.playerId === {{ $this->player->id }} ? 'bg-beige' : 'bg-forest-green'"
                    :style="`
                        transform: translate(${tile.x}px, ${tile.y}px) scale(${tile.scale || 1});
                        opacity: ${tile.opacity === undefined ? 1 : tile.opacity};
                    `"
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
                    <div>
                        <button 
                            @click="playTile('from_right', i-1, {{ $this->player->id }}); $wire.playTile('left', i)"
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
                            @click="playTile('up', i-1, {{ $this->player->id }}); $wire.playTile('up', i)"
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