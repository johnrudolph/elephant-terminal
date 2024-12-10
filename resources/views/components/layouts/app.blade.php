<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name', 'Elephant in the Room') }}{{ !empty($title) ? ' - ' . $title : '' }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxStyles
        @livewireScripts
    </head>
    <body class="min-h-screen bg-white dark:bg-deep-purple text-zinc-800 dark:text-zinc-200">
        @if (auth()->user())
        <flux:header container class="bg-zinc-50 border-b border-zinc-200 dark:bg-pink dark:border-zinc-700">
            <flux:navbar class="-mb-px">
                <flux:navbar.item icon="home" href="/dashboard">Home</flux:navbar.item>
                <flux:navbar.item icon="users" href="/friends">Friends</flux:navbar.item>
                </flux:navbar>

                <flux:spacer />

                <flux:navbar>
                    <flux:navbar.item icon="cog-6-tooth" href="/profile" label="Settings" />
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <flux:navbar.item 
                            icon="power" 
                            as="button"
                            type="submit"
                            label="Logout" 
                        />
                    </form>
                </flux:navbar>
            </flux:header>
        @endif

        <flux:main container class="max-w-screen-sm">
            <div class="flex flex-col pb-16">
                {{ $slot }}
            </div>
            <x-footer />
        </flux:main>

        @fluxScripts
    </body>
</html>
