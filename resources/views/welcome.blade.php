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
                    <img 
                        src="{{ asset('img/Gojo.png') }}" 
                        alt="Gojo" 
                        class="absolute bottom-0 left-1/2 -translate-x-1/2 w-auto max-h-full"
                    >
                </div>

                <div class="flex flex-col items-center justify-center p-8">
                    <h2 class="text-green-500 font-mono text-center text-2xl mb-8 uppercase tracking-widest">some other shit here</h2>
                    <p class="text-white/90 font-mono text-lg leading-relaxed">
                        This is not a joke anymore
                        I swear to god <span class="text-green-400">TheOneGreenBear</span> he is really gonna do it!
                    </p>
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