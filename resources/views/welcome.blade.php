<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|bodoni-moda:500,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
<body class="grain antialiased h-screen w-full relative overflow-hidden bg-sand text-ink">
    {{-- Fog backdrop behind the window (clipped so it can never widen the page). --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="bg-fog absolute inset-0"></div>
    </div>

    <!-- Fog-paper window over the drifting fog -->
    <div class="absolute inset-3 md:inset-10 lg:inset-16 border border-ink/15 bg-paper window-frame overflow-y-auto no-scrollbar scroll-smooth" id="scroll-container" style="scroll-snap-type: none;">

    <!-- Navbar: sticks to the top of the window frame itself, so it always lines up with it.
         The zero-height wrapper means it floats over the hero instead of pushing it down. -->
    <div class="sticky top-0 z-40 h-0">
        <x-navbar />
    </div>

    <!-- Hero: an intel-file title card. Title and intro on the left, the green bear with a blurred
         ghost of itself on the right, tied together by thin HUD lines that draw themselves in on wider
         screens. A crosshair tracks the mouse (resources/js/Hud.js) and a scan line sweeps down. -->
    <div data-snap data-crosshair-area class="bg-hero-fog relative flex min-h-full w-full items-center justify-center overflow-hidden px-6 pt-24 pb-28 md:px-16" style="scroll-snap-align: start; scroll-snap-stop: normal;">

        <x-hud-lines class="pointer-events-none absolute inset-0 hidden h-full w-full text-ink md:block" />

        <span class="hud-scan pointer-events-none absolute inset-x-0 top-0 h-32" aria-hidden="true"></span>

        <!-- Crosshair: hairlines across the hero and a rotating square where they cross. -->
        <div class="pointer-events-none absolute inset-0 hidden md:block" aria-hidden="true">
            <span data-crosshair-x class="hud-crosshair absolute top-0 left-0 h-px w-full bg-ink/15"></span>
            <span data-crosshair-y class="hud-crosshair absolute top-0 left-0 h-full w-px bg-ink/15"></span>
            <span data-crosshair-box class="hud-crosshair absolute top-0 left-0">
                <span class="hud-reticle absolute -top-3 -left-3 block size-6 border border-ink/60"></span>
            </span>
        </div>
        <p data-hud-coords class="pointer-events-none absolute right-8 bottom-8 hidden font-mono text-[10px] tracking-[0.3em] text-ink/50 md:block" aria-hidden="true">X 0000 · Y 0000</p>

        <div class="relative z-10 grid w-full max-w-6xl items-center gap-10 md:grid-cols-[1.1fr_1fr]">
            <div class="order-2 md:order-1">
                <p class="flex items-center gap-3 font-mono text-[10px] uppercase tracking-[0.3em] text-ink/70">
                    <span class="bg-ink px-1.5 py-0.5 text-paper">01</span>
                    <span data-scramble data-scramble-delay="200">Subject file</span>
                </p>

                <p data-scramble data-scramble-delay="400" class="mt-8 font-mono text-sm uppercase tracking-[0.8em] text-ink md:text-base">The one</p>
                <h1 class="glitch-title mt-2 font-display text-6xl uppercase leading-[0.9] text-ink sm:text-7xl lg:text-8xl">
                    Green<br>bear<span class="text-azuki">.</span>
                </h1>

                <p class="mt-8 max-w-sm text-justify font-mono text-[11px] uppercase leading-relaxed tracking-wide text-ink/70">
                    Games, anime, music and code. What I play, watch, listen to and build, pulled live from
                    Steam, MyAnimeList, Spotify and GitHub.
                </p>

                <div class="mt-8 flex items-center gap-3">
                    <a href="https://discord.com" target="_blank" rel="noopener" aria-label="Discord" class="bg-ink p-2.5 transition hover:bg-azuki">
                        <img src="{{ asset('img/DiscordLogo.png') }}" alt="" class="size-6">
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Steam" class="bg-ink p-2.5 transition hover:bg-azuki">
                        <img src="{{ asset('img/SteamLogo.png') }}" alt="" class="size-6">
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Twitch" class="bg-ink p-2.5 transition hover:bg-azuki">
                        <img src="{{ asset('img/TwitchLogo.png') }}" alt="" class="size-6">
                    </a>
                </div>
            </div>

            <!-- The bear: blurred grey ghost behind, the green bear in front, corner brackets around it. -->
            <div class="relative order-1 mx-auto md:order-2" style="width: min(380px, 62vw, 40vh);">
                <img src="{{ asset('img/TheOneGreenBearLogo.png') }}" alt="" aria-hidden="true" class="bear-ghost pointer-events-none absolute inset-0 w-full translate-x-[14%] translate-y-[4%] scale-125">

                <div class="parallax-logo relative">
                    <img src="{{ asset('img/TheOneGreenBearLogo.png') }}" id="parallax-logo" alt="" class="w-full">
                </div>

                <span class="absolute -top-4 -left-4 size-4 border-t border-l border-ink/60" aria-hidden="true"></span>
                <span class="absolute -top-4 -right-4 size-4 border-t border-r border-ink/60" aria-hidden="true"></span>
                <span class="absolute -bottom-4 -left-4 size-4 border-b border-l border-ink/60" aria-hidden="true"></span>
                <span class="absolute -right-4 -bottom-4 size-4 border-r border-b border-ink/60" aria-hidden="true"></span>
            </div>
        </div>

        <!-- scroll down arrow -->
        <x-scrolldown-arrow />

    </div>

    <!-- Explore: numbered bento grid -->
    <section data-snap class="relative flex min-h-full w-full flex-col border-t border-ink/10 bg-paper px-6 pt-24 pb-16 md:px-12 lg:px-16" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <header class="mb-6 md:mb-8">
            <h2 class="font-display text-4xl uppercase leading-none text-ink md:text-6xl">Explore <span class="text-azuki">//</span></h2>
            <p class="mt-4 max-w-xl font-mono text-[11px] uppercase leading-relaxed tracking-[0.3em] text-ink/60">
                Games, anime, music and code, kept up to date automatically.
            </p>
        </header>

        <div class="grid flex-1 grid-cols-2 gap-2 md:grid-cols-4 md:grid-rows-2">
            <x-bento-tile number="01" label="Projects" :href="route('projects')" class="md:row-span-2"
                :status="$latestProject ? 'Latest: '.$latestProject->displayName() : 'Things I\'ve built'">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="02" label="Games" :href="route('games')"
                :status="$lastPlayed ? 'Last played: '.$lastPlayed->name : 'What I play'">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="6"/><path d="M6 12h4M8 10v4"/><circle cx="15" cy="11" r="0.8"/><circle cx="17.5" cy="13" r="0.8"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="03" label="Anime" :href="route('anime')"
                :status="$nowWatching ? 'Watching: '.$nowWatching->title : 'What I watch'">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M8 3l4 4 4-4M10 11v6l5-3z"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="04" label="Spotify" :href="route('spotify')" class="md:row-span-2"
                :status="$listening ? $listening['label'].': '.$listening['track']['name'].' – '.$listening['track']['artists'] : 'What I listen to'">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="05" label="Gym" status="Progress coming soon" class="col-span-2 md:col-span-1">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="7" width="3" height="10" rx="1"/><rect x="17" y="7" width="3" height="10" rx="1"/><path d="M7 12h10M2 10v4M22 10v4M2 12h2M20 12h2"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="06" label="Connect" accent class="col-span-2 md:col-span-1">
                <div class="relative mt-4 flex gap-3">
                    <a href="#" target="_blank" rel="noopener" aria-label="X" class="rounded-sm bg-paper/15 p-2 transition hover:bg-paper hover:text-azuki">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="TikTok" class="rounded-sm bg-paper/15 p-2 transition hover:bg-paper hover:text-azuki">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram" class="rounded-sm bg-paper/15 p-2 transition hover:bg-paper hover:text-azuki">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </x-bento-tile>
        </div>

        <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

     <section data-snap class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-sand" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="font-display text-3xl md:text-5xl mb-8 uppercase leading-none text-ink">About me <span class="text-azuki">//</span></h2>
            <p class="text-ink/90 font-mono text-lg leading-relaxed">
                Coming soon from <span class="text-azuki">TheOneGreenBear</span>.
            </p>
        </div>

         <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

    <section data-snap class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-paper" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="font-display text-3xl md:text-5xl mb-8 uppercase leading-none text-ink">Contact <span class="text-azuki">//</span></h2>
            <p class="text-ink/90 font-mono text-lg leading-relaxed">
                Coming soon.
            </p>
        </div>
    </section>

    </div>
</body>
