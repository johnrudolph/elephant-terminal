<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxStyles
        @livewireScripts
    </head>
    <body class="min-h-screen bg-white">
        <flux:header container class="bg-zinc-50 border-b border-zinc-200">
            <flux:sidebar.toggle class="md:hidden" icon="bars-2" inset="left" />

            <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-md:hidden" />
            <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="max-lg:!hidden hidden" />

            <flux:navbar class="-mb-px max-md:hidden">
                <flux:navbar.item icon="home" href="/dashboard" current>Home</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="mr-4">
                <flux:navbar.item class="max-md:hidden" icon="cog-6-tooth" href="/profile" label="Settings" />
            </flux:navbar>
        </flux:header>

        <flux:sidebar stashable sticky class="md:hidden bg-zinc-50 border-r border-zinc-200">
            <flux:sidebar.toggle class="md:hidden" icon="x-mark" />

            <flux:brand href="#" name="Elephant in the Room" class="px-2" />
            <flux:brand href="#" name="Elephant in the Room" class="px-2 hidden" />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" href="/dashboard" current>Home</flux:navlist.item>

                <flux:navlist.group expandable heading="Favorites" class="max-md:hidden">
                    <flux:navlist.item href="#">Marketing site</flux:navlist.item>
                    <flux:navlist.item href="#">Android app</flux:navlist.item>
                    <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="cog-6-tooth" href="profile">Settings</flux:navlist.item>
                <flux:navlist.item icon="arrow-right-start-on-rectangle" href="/logout">Logout</flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @fluxScripts
    </body>
</html>
