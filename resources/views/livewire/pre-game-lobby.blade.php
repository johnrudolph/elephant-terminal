<div>
    @if($this->game->players->count() === 1 && $this->player)
        <flux:card class="w-full">
            <flux:heading>Waiting for opponent</flux:heading>
            <flux:subheading>Share this link to invite a friend.</flux:subheading>
            {{-- @todo sometimes this doesn't work?? --}}
            <flux:input class="mt-4" value="{{ route('games.pre-game-lobby.show', $game->id) }}" readonly copyable />
        </flux:card>
    @endif

    @if($this->game->players->count() === 1 && ! $this->player)
        <flux:card class="w-full">
            <flux:heading>Awaiting opponent</flux:heading>
            <flux:button class="mt-4" variant="primary" wire:click="join">Join</flux:button>
        </flux:card>
    @endif

    @if($this->game->players->count() === 2 && $this->player)
        <flux:card class="w-full">
            <flux:heading>Everyone's here!</flux:heading>
            <flux:button class="mt-4" variant="primary" wire:click="start">Start</flux:button>
        </flux:card>
    @endif
</div>
