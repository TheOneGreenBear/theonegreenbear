<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|archivo-black:400" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .bear-swap img { transition: opacity 400ms ease-in-out; }
            .bear-swap .bear-alt { opacity: 0; }
            .bear-swap.is-alt .bear-main { opacity: 0; }
            .bear-swap.is-alt .bear-alt { opacity: 1; }
        </style>
    </head>
    
<body class="antialiased h-screen w-full relative overflow-hidden">
    <div class="absolute inset-0 WelcomePagebg-image blur-lg scale-105"></div>
        
    <div class="absolute inset-3 md:inset-20 border-4 border-white/50 overflow-y-auto group no-scrollbar scroll-smooth" id="scroll-container" style="scroll-snap-type: none;">
        
    <div class="absolute inset-0 WelcomePagebg-image"></div>
    

    <!-- Navbar Component (fixed at top, doesn't affect scroll layout) -->
    <div class="fixed z-40 top-3 left-3 right-3 md:top-20 md:left-20 md:right-20">
        <x-navbar />
    </div>

    <!-- TheOneGreenBear Text Logo -->
    <div class="absolute inset-1 pointer-events-none">
        <img 
            src="{{ url(asset('img/TOGBText.png')) }}" 
            alt="Foreground Leaves" 
            class="absolute top-[2%] left-1/2 transform -translate-x-1/2 w-full max-w-4xl Image-Float-Effect pointer-events-none"
        >
    </div>

    <!-- TheOneGreenBear Head logo -->
    <div class="h-full w-full flex flex-col items-center justify-center relative" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        
        <div class="p-6 parallax-logo relative z-20">
            <img 
                src="{{ asset('img/TheOneGreenBearLogo.png') }}" 
                id="parallax-logo"
                alt="Logo" 
                style="max-width: 450px; width: 80vw; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.5));"
            >
        </div>

        <div class="flex items-center justify-center gap-6 mt-4 z-30">
            <a href="https://discord.com" target="_blank" class="transition hover:scale-110">
                <img class="bg-stone-950/50 p-3 hover:bg-indigo-600 rounded-xl border border-white/10" 
                    src="{{ asset('img/DiscordLogo.png') }}" 
                    alt="Discord" 
                    style="max-width: 60px;">
            </a>
            
            <a href="#" target="_blank" class="transition hover:scale-110">
                <img class="bg-stone-950/50 p-3 hover:bg-blue-500 rounded-xl border border-white/10" 
                    src="{{ asset('img/SteamLogo.png') }}" 
                    alt="Steam" 
                    style="max-width: 60px;">
            </a>

            <a href="#" target="_blank" class="transition hover:scale-110">
                <img class="bg-stone-950/50 p-3 hover:bg-purple-600 rounded-xl border border-white/10" 
                    src="{{ asset('img/TwitchLogo.png') }}" 
                    alt="Twitch" 
                    style="max-width: 60px;">
            </a>
        </div>

        <!-- scroll down arrow -->
        <x-scrolldown-arrow />

    </div>

    <!-- Explore: numbered bento grid -->
    <section class="relative flex min-h-full w-full flex-col bg-black/80 px-6 pt-24 pb-16 backdrop-blur-md md:px-12 lg:px-16" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <header class="mb-6 md:mb-8">
            <h2 class="font-display text-4xl uppercase leading-none text-white md:text-6xl">Explore <span class="text-[#08a88a]">//</span></h2>
            <p class="mt-4 max-w-xl font-mono text-[11px] uppercase leading-relaxed tracking-[0.3em] text-white/60">
                Games, anime, music and code, kept up to date automatically.
            </p>
        </header>

        <div class="grid flex-1 grid-cols-2 gap-2 md:grid-cols-5 md:grid-rows-2">
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

            <!-- The bear: tall tile, click to swap poses -->
            <div class="bear-swap group relative col-span-2 row-span-2 hidden cursor-pointer overflow-hidden rounded-sm border border-white/5 bg-gradient-to-b from-azukigray-900/40 to-[#08a88a]/10 md:col-span-1 md:block"
                 onclick="this.classList.toggle('is-alt');" title="Click me">
                {{-- The first pose has wide empty margins, so crop it (cover); the second is already tight (contain). --}}
                <img src="{{ asset('img/GreenBearMan.png') }}" alt="TheOneGreenBear" class="bear-main absolute inset-0 h-full w-full object-cover object-center p-2 transition duration-500 group-hover:scale-105">
                <img src="{{ asset('img/GreenBearMan2.png') }}" alt="" class="bear-alt absolute inset-0 h-full w-full object-contain object-bottom p-3 transition duration-500 group-hover:scale-105">
            </div>

            <x-bento-tile number="05" label="Gym" status="Progress coming soon" class="col-span-2 md:col-span-1">
                <x-slot:icon>
                    <svg class="size-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="7" width="3" height="10" rx="1"/><rect x="17" y="7" width="3" height="10" rx="1"/><path d="M7 12h10M2 10v4M22 10v4M2 12h2M20 12h2"/></svg>
                </x-slot:icon>
            </x-bento-tile>

            <x-bento-tile number="06" label="Connect" accent class="col-span-2 md:col-span-1">
                <div class="relative mt-4 flex gap-3">
                    <a href="#" target="_blank" rel="noopener" aria-label="X" class="rounded-sm bg-[#09090b]/10 p-2 transition hover:bg-[#09090b] hover:text-[#08a88a]">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="TikTok" class="rounded-sm bg-[#09090b]/10 p-2 transition hover:bg-[#09090b] hover:text-[#08a88a]">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram" class="rounded-sm bg-[#09090b]/10 p-2 transition hover:bg-[#09090b] hover:text-[#08a88a]">
                        <svg class="size-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </x-bento-tile>
        </div>

        <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

     <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/40 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">About me</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                Coming soon from <span class="text-green-400">TheOneGreenBear</span>.
            </p>
        </div>

         <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

    <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/60 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">Contact</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                Coming soon.
            </p>
        </div>
    </section>

    </div>
</body>
