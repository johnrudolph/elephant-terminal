<div>
    <flux:fieldset>
        <flux:legend>Game settings</flux:legend>

        <div class="space-y-3">
            <flux:field variant="inline" class="w-full flex justify-between">
                <flux:switch wire:model.live="is_bot_game" />
                <flux:label>Versus bot</flux:label>
            </flux:field>

            <flux:field variant="inline" class="w-full flex justify-between">
                <flux:switch wire:model.live="is_ranked_game" disabled />
                <flux:label>Ranked</flux:label>
            </flux:field>

            <flux:field variant="inline" class="w-full flex justify-between">
                <flux:switch wire:model.live="is_friends_only" />
                <flux:label>Friends only</flux:label>
            </flux:field>
        </div>
    </flux:fieldset>
    <flux:button wire:click="newGame" class="mt-5">New game</flux:button>
</div>
