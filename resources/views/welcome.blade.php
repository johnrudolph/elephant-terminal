<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxStyles
        @livewireScripts
    </head>
    <body class="antialiased font-sans bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
        <flux:container class="flex flex-col items-center justify-center">
            <flux:card class="max-w-xl mt-16">
                <flux:heading size="xl">
                    Elephant in the Room
                </flux:heading>
                <flux:subheading>
                    Your mission: build a simple shape. The catch? Your opponent can push your tiles around. And there's an elephant in the room.
                </flux:subheading>
            </flux:card>
            <x-demo />
            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                Made with ❤️ by <a href="https://catacombian.com"><span class="font-bold text-zinc-900 dark:text-zinc-200">Catacombian Games</span></a>
            </footer>
        </flux:container>
        @fluxScripts
    </body>
</html>
