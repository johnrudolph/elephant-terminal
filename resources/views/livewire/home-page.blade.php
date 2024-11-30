<div>
    <flux:fieldset>
        <flux:legend>Game settings</flux:legend>

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
    <flux:button wire:click="newGame" class="mt-5">New game</flux:button>
</div>
