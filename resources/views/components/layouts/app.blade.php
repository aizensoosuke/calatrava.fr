@props([
    'title' => null,
    'withHeader' => true,
    'withFooter' => true,
])

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>
            @if(! is_null($title))
                {{ $title }} |
            @endif
            Calatrava
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- GlideJS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">

        @vite('resources/js/app.js')
        @vite('resources/css/app.css')
    </head>
    <body id="app" class="relative min-vh-100 min-w-[320px] overflow-x-scroll font-display antialiased">
        @if($withHeader)
            <x-layouts.sections.header />
        @endif

        <div id="navigation-dropdown" class="relative z-40">{{-- For @teleport --}}</div>

        {{ $slot }}

        @if($withFooter)
            <x-layouts.sections.footer />
        @endif

        @livewireScripts
    </body>
</html>
