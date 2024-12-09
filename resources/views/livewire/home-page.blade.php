<div>
    <flux:tab.group>
        <flux:tabs class="px-4">
            <flux:tab name="join">Join</flux:tab>
            <flux:tab name="create">Create</flux:tab>
            <flux:tab name="rules">Rules</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="create">
            <flux:fieldset>
                <div class="space-y-3">
                    <flux:field variant="inline" class="w-full flex justify-between">
                        <flux:switch 
                            wire:model.live="is_bot_game" 
                            x-on:change="
                                if ($event.target.checked) {
                                    $wire.is_ranked_game = false;
                                    $wire.is_friends_only = false;
                                }
                            "
                        />
                        <flux:label>Versus bot</flux:label>
                    </flux:field>

                    <flux:field variant="inline" class="w-full flex justify-between">
                        <flux:switch 
                            wire:model.live="is_ranked_game" 
                            x-bind:disabled="$wire.is_bot_game"
                        />
                        <flux:label>Ranked</flux:label>
                    </flux:field>

                    <flux:field variant="inline" class="w-full flex justify-between">
                        <flux:switch 
                            wire:model.live="is_friends_only" 
                            x-bind:disabled="$wire.is_bot_game"
                        />
                        <flux:label>Friends only</flux:label>
                    </flux:field>
                </div>
            </flux:fieldset>
            <flux:button wire:click="newGame" variant="primary" class="mt-8">Start game</flux:button>
        </flux:tab.panel>

        <flux:tab.panel name="join">
            @if ($this->games->isEmpty())
                <flux:heading>No active games</flux:heading>
            @else
                <flux:table>
                    <flux:columns>
                        <flux:column></flux:column>
                        <flux:column></flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach ($this->games as $game)
                            <div wire:key="game-{{ $game['id'] }}">
                                <flux:row>
                                    <flux:cell>
                                        <div class="flex items-center gap-2">
                                            {{ $game['player'] }}
                                            <flux:badge color="gray" size="sm" variant="outline">{{ $game['rating'] }}</flux:badge>
                                            @if ($game['is_friend'])
                                                <flux:badge size="xs" color="green">Friend</flux:badge>
                                            @endif
                                        </div>
                                    </flux:cell>
                                    <flux:cell class="flex justify-end">
                                        <flux:button wire:click="join('{{ $game['id'] }}')" variant="primary" size="xs">Join</flux:button>
                                    </flux:cell>
                                </flux:row>
                            </div>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="rules">
            <flux:card>
                <flux:subheading>
                    Take turns sliding a tile onto the board. Sliding tiles push other tiles into the next space. Tiles pushed off the the board return to your hand. The elephant blocks slides. Move the elephant each time you play a tile. If you are out of tiles, you skip your turn. This usually means you'll lose. The first player to create their victory shape wins.
                </flux:subheading>
            </flux:card>
        </flux:tab.panel>
    </flux:tab.group>
</div>
