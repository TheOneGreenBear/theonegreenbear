<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/Parallax.js', 'resources/js/SmoothScroll.js'])

        <style>
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .bear-swap img { transition: opacity 400ms ease-in-out; }
            .bear-swap .bear-alt { opacity: 0; }
            .bear-swap.is-alt .bear-main { opacity: 0; }
            .bear-swap.is-alt .bear-alt { opacity: 1; }
        </style>
    </head>
    
<body class="antialiased h-screen w-full relative overflow-hidden">
    <div class="absolute inset-0 WelcomePagebg-image blur-lg scale-105"></div>
        
    <div class="absolute inset-20 border-4 border-white/50 overflow-y-auto group no-scrollbar scroll-smooth" id="scroll-container" style="scroll-snap-type: none;">
        
    <div class="absolute inset-0 WelcomePagebg-image"></div>
    

    <!-- Navbar Component (fixed at top, doesn't affect scroll layout) -->
    <div class="fixed top-0 left-0 right-0 z-40" style="margin-left: 5rem; margin-right: 5rem; margin-top: 5rem;">
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

    <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/80 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="w-screen h-9/10 text-center border border-white/10 rounded-lg overflow-hidden">
            <div class="grid grid-cols-[60%_40%] h-full bg-white/5">
                <div class="relative overflow-hidden flex items-center justify-center">
                    <div
                        class="absolute inset-0 mx-auto my-auto h-full w-full object-contain translate-y-15 bear-swap cursor-pointer"
                        onclick="this.classList.toggle('is-alt');"
                    >
                        <img 
                            src="{{ asset('img/GreenBearMan.png') }}" 
                            alt="Green Bear Man" 
                            class="absolute inset-0 mx-auto my-auto h-full w-full object-contain bear-main"
                        >
                        <img 
                            src="{{ asset('img/GreenBearMan2.png') }}" 
                            alt="Green Bear Man Alternate" 
                            class="absolute inset-0 mx-auto my-auto h-full w-full object-contain bear-alt"
                        >
                    </div>
                </div>

                <div class="flex flex-col gap-2 p-2 h-full">
                    <section class="relative bg-[#09090b] p-4 rounded-md w-full flex flex-col justify-end items-start flex-1 hover:bg-azukigray-400 transition hover:[&_h3]:text-[#09090b] hover:[&_p]:text-[#09090b]">
                        <h3 class="text-white font-mono text-lg mb-1 uppercase tracking-wider transition-colors">Games</h3>
                        <p class="text-white font-mono text-xs leading-relaxed opacity-80 transition-colors">
                            Games that I play
                        </p>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 430 430"><path stroke="#08a88a" stroke-linejoin="round" stroke-width="12" d="M45 75c0-11.046 8.954-20 20-20h300c11.046 0 20 8.954 20 20v220c0 11.046-8.954 20-20 20H65c-11.046 0-20-8.954-20-20z"/><path stroke="#08a88a" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m185 315-5 60m65-60 5 60"/><path stroke="#08a88a" stroke-linecap="round" stroke-width="12" d="M285 375H145m240-120H45"/><circle cx="215" cy="285" r="10" fill="#08a88a"/></svg>
                        </div>
                    </section>

                    <section class="relative bg-[#09090b] p-4 rounded-md w-full flex flex-col justify-end items-start flex-1 hover:bg-azukigray-400 transition hover:[&_h3]:text-[#09090b] hover:[&_p]:text-[#09090b]">
                        <h3 class="text-white font-mono text-lg mb-1 uppercase tracking-wider transition-colors">Projects</h3>
                        <p class="text-white font-mono text-xs leading-relaxed opacity-80 transition-colors">
                            My personal projects
                        </p>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 430 430"><path stroke="#08a88a" stroke-linejoin="round" stroke-width="12" d="M45 75c0-11.046 8.954-20 20-20h300c11.046 0 20 8.954 20 20v220c0 11.046-8.954 20-20 20H65c-11.046 0-20-8.954-20-20z"/><path stroke="#08a88a" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m185 315-5 60m65-60 5 60"/><path stroke="#08a88a" stroke-linecap="round" stroke-width="12" d="M285 375H145m240-120H45"/><circle cx="215" cy="285" r="10" fill="#08a88a"/></svg>
                        </div>
                    </section>

                    <section class="relative bg-[#09090b] p-4 rounded-md w-full flex flex-col justify-end items-start flex-1 hover:bg-azukigray-400 transition hover:[&_h3]:text-[#09090b] hover:[&_p]:text-[#09090b]">
                        <h3 class="text-white font-mono text-lg mb-1 uppercase tracking-wider transition-colors">GYM</h3>
                        <p class="text-white font-mono text-xs leading-relaxed opacity-80 transition-colors">
                            Gym progress
                        </p>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 430 430"><path stroke="#08a88a" stroke-linejoin="round" stroke-width="12" d="M45 75c0-11.046 8.954-20 20-20h300c11.046 0 20 8.954 20 20v220c0 11.046-8.954 20-20 20H65c-11.046 0-20-8.954-20-20z"/><path stroke="#08a88a" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m185 315-5 60m65-60 5 60"/><path stroke="#08a88a" stroke-linecap="round" stroke-width="12" d="M285 375H145m240-120H45"/><circle cx="215" cy="285" r="10" fill="#08a88a"/></svg>
                        </div>
                    </section>

                    <section class="relative bg-[#09090b] p-4 rounded-md w-full flex flex-col justify-end items-start flex-1 hover:bg-azukigray-400 transition hover:[&_h3]:text-[#09090b] hover:[&_p]:text-[#09090b]">
                       <h3 class="text-white font-mono text-lg mb-1 uppercase tracking-wider transition-colors">MyAnimeList</h3>
                        <p class="text-white font-mono text-xs leading-relaxed opacity-80 transition-colors">
                           List of anime I've watched and currently watching.
                        </p>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 430 430"><path stroke="#08a88a" stroke-linejoin="round" stroke-width="12" d="M45 75c0-11.046 8.954-20 20-20h300c11.046 0 20 8.954 20 20v220c0 11.046-8.954 20-20 20H65c-11.046 0-20-8.954-20-20z"/><path stroke="#08a88a" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m185 315-5 60m65-60 5 60"/><path stroke="#08a88a" stroke-linecap="round" stroke-width="12" d="M285 375H145m240-120H45"/><circle cx="215" cy="285" r="10" fill="#08a88a"/></svg>
                        </div>
                    </section>

                    <section class="relative bg-[#09090b] p-4 rounded-md w-full flex flex-col justify-end items-start flex-1 hover:bg-azukigray-400 transition hover:[&_h3]:text-[#09090b] hover:[&_p]:text-[#09090b]">
                       <h3 class="text-white font-mono text-lg mb-1 uppercase tracking-wider transition-colors">Spotify</h3>
                        <p class="text-white font-mono text-xs leading-relaxed opacity-80 transition-colors">
                            My playlists and favorite songs.
                        </p>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 w-20 h-20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 430 430"><path stroke="#08a88a" stroke-linejoin="round" stroke-width="12" d="M45 75c0-11.046 8.954-20 20-20h300c11.046 0 20 8.954 20 20v220c0 11.046-8.954 20-20 20H65c-11.046 0-20-8.954-20-20z"/><path stroke="#08a88a" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m185 315-5 60m65-60 5 60"/><path stroke="#08a88a" stroke-linecap="round" stroke-width="12" d="M285 375H145m240-120H45"/><circle cx="215" cy="285" r="10" fill="#08a88a"/></svg>
                        </div>
                    </section>

                      <section class="rounded-md w-full flex gap-1 flex-1">
                        <div class="flex-1 bg-[#09090b] p-4 rounded-lg flex items-center justify-center text-white hover:bg-azukigray-400 hover:text-[#09090b] transition">
                        <a href="#" target="_blank">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>

                        </div>
                        
                        <div class="flex-1 bg-[#09090b] p-4 rounded-lg flex items-center justify-center text-white hover:bg-azukigray-400 hover:text-[#09090b] transition">
                        <a href="#" target="_blank">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                        </a>
                        </div>
                        
                        
                        <div class="flex-1 bg-[#09090b] p-4 rounded-lg flex items-center justify-center text-white hover:bg-azukigray-400 hover:text-[#09090b] transition">
                            <a href="#" target="_blank">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        </div>
                    
                    </section>
                </div>
            </div>
        </div>

        <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

     <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/40 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">Pussy!</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                This pussy is like the matrix. 
                Because <span class="text-green-400">TheOneGreenBear</span> is in that bitch and he can't get out. 
            </p>
        </div>

         <!-- scroll down arrow -->
        <x-scrolldown-arrow />
    </section>

    <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/60 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: normal;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">I need fentanyl</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                Rome wasn't built in a day but this 9mm was! Give me the fentanyl now!
            </p>
        </div>
    </section>

    </div>
</body>