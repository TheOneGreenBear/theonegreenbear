<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/Parallax.js'])

        <style>
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
        </style>
    </head>
    
<body class="antialiased h-screen w-full relative overflow-hidden">
    <div class="absolute inset-0 WelcomePagebg-image blur-lg scale-105"></div>
        
    <div class="absolute inset-20 border-4 border-white/50 overflow-y-auto group no-scrollbar" style="scroll-snap-type: y mandatory; scroll-behavior: auto;">
        
    <div class="absolute inset-0 WelcomePagebg-image"></div>
    

    <!-- Navbar Component (absolute so it doesn't affect snap layout) -->
    <div class="absolute top-0 left-0 right-0 z-40">
        <x-navbar />
    </div>

    <!-- TheOneGreenBear Text Logo -->
    <div class="absolute inset-1 pointer-events-none">
        <img 
            src="{{ url(asset('img/TOGBText.png')) }}" 
            alt="Foreground Leaves" 
            class="absolute top-[7%] left-1/2 transform -translate-x-1/2 w-full max-w-4xl Image-Float-Effect pointer-events-none"
        >
    </div>

    <!-- TheOneGreenBear Head logo -->
    <div class="h-full w-full flex flex-col items-center justify-center relative" style="scroll-snap-align: start; scroll-snap-stop: always;">
        
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
                    src="https://upload.wikimedia.org/wikipedia/commons/8/83/Steam_icon_logo.svg" 
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

    </div>

    <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/60 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: always;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">I might kms</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                This is not a joke anymore
                Deployment of <span class="text-green-400">TheOneGreenBear</span> he is really gonna do it
            </p>
        </div>
    </section>

    <section class="relative h-full w-full box-border flex items-center justify-center p-8 md:p-24 snap-start bg-black/60 backdrop-blur-md" style="scroll-snap-align: start; scroll-snap-stop: always;">
        <div class="max-w-3xl text-center">
            <h2 class="text-green-500 font-mono text-2xl mb-8 uppercase tracking-widest">Tactical Briefing</h2>
            <p class="text-white/90 font-mono text-lg leading-relaxed">
                This interface is optimized for high-fidelity communication. 
                Deployment of <span class="text-green-400">The One Green Bear</span> assets is monitored. 
                Ensure all protocols are followed during your session.
            </p>
        </div>
    </section>

    </div>

</body>
</html>