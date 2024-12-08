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
    <body class="min-h-screen bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
        <flux:header container class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:navbar class="-mb-px">
                <flux:navbar.item icon="home" href="/dashboard">Home</flux:navbar.item>
                <flux:navbar.item icon="users" href="/friends">Friends</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="mr-4">
                <flux:navbar.item class="max-md:hidden" icon="cog-6-tooth" href="/profile" label="Settings" />
            </flux:navbar>
        </flux:header>

        <flux:main container class="max-w-screen-sm">
            {{ $slot }}
        </flux:main>

        <footer class="py-16 text-center text-sm text-black dark:text-white/70">
            Made with ❤️ by <a href="https://catacombian.com"><span class="font-bold text-zinc-900 dark:text-zinc-200">Catacombian Games</span></a>
        </footer>

        @fluxScripts
    </body>
</html>
