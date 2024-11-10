@props(['player', 'player_color' => ''])

<flux:card class="w-full">
    <div class="flex flex-row justify-between items-center text-zinc-800 dark:text-zinc-200">
        <div class="flex flex-col items-start w-full">
            <flux:heading class="text-left w-full">
                {{ $player->user->name }}
            </flux:heading>
            <div class="mt-2 flex flex-row space-x-2 items-center">
                <div class="{{ $player_color }} w-8 h-8 rounded-lg flex justify-center">
                    <p class="font-bold" x-text="$el.dataset.hand"></p>
                </div>
                <p class="text-xs">remaining</p>
            </div>
        </div>
        <x-dynamic-component 
            :component="'svg.' . $player->victory_shape"
            class="w-14 h-14"
        />
    </div>
</flux:card>
