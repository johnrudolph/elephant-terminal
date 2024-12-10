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
        @fluxStyles
    </head>
    <body class="antialiased font-sans bg-beige dark:bg-deep-purple text-deep-purple dark:text-beige">
        <flux:container class="flex flex-col items-center justify-center">
            <flux:card class="max-w-xs mt-16">
                <flux:heading size="xl">
                    Elephant in the Room
                </flux:heading>
                <flux:subheading>
                    Your mission: build a simple shape. The catch? Your opponent can push your tiles around, and there's an elephant in the room.
                </flux:subheading>
                <div class="mt-4 flex flex-row space-x-4 justify-center">
                    @if (auth()->user())
                        <flux:button variant="primary" href="{{ route('dashboard') }}">Play</flux:button>
                    @else
                        <flux:button variant="primary" href="{{ route('register') }}">Register</flux:button>
                        <flux:button variant="filled" href="{{ route('login') }}">Login</flux:button>
                    @endif
                </div>
            </flux:card>
            <div class="mt-16">
                <x-demo />
            </div>
            <footer class="fixed bottom-0 left-0 right-0 py-4 text-center text-sm">
                Made with ❤️ by <a href="https://catacombian.com"><span class="font-bold">Catacombian Games</span></a>
            </footer>
        </flux:container>
        @fluxScripts
    </body>
</html>
