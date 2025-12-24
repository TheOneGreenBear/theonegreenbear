<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/Parallax.js'])
    </head>
    
<body class="antialiased h-screen w-full relative overflow-hidden">
    <div class="absolute inset-0 WelcomePagebg-image blur-lg scale-105"></div>
        
    <div class="absolute inset-20 border-4 border-white/50 overflow-hidden group">
        
    <div class="absolute inset-0 WelcomePagebg-image"></div>
    
        <x-navbar />

    </div>

    <div class="absolute inset-1 z-1 pointer-events-none">

        <img 
            src="{{ url('https://www.azuki.com/beanz-page/beanz-logo.png') }}" 
            alt="Foreground Leaves" 
            class="absolute top-[11%] left-1/2 transform -translate-x-1/2 w-full max-w-4xl Image-Float-Effect pointer-events-none"
        >

    </div>

    <div class="h-full w-full flex items-center justify-center">
        <div class="p-6 parallax-logo relative z-20">
            <img 
                src="{{ asset('img/TheOneGreenBearLogo.png') }}" 
                id="parallax-logo"
                alt="Logo" 
                style="max-width: 500px; width: 100vw; filter: drop-shadow(0 20px 50px rgba(0,0,0,0.5));"
            >
            <div class="h-full w-full flex items-center justify-center gap-4">
                <input class="bg-stone-950 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded z-1" type="image" src="{{ asset('img/DiscordLogo.png') }}" alt="Submit" 
                    style="max-width: 65px; width: 50vw";
                />
                 <input class="bg-stone-950 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded z-1" type="image" src="https://upload.wikimedia.org/wikipedia/commons/8/83/Steam_icon_logo.svg" alt="Submit" 
                    style="max-width: 65px; width: 50vw";
                />
                 <input class="bg-stone-950 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded z-1" type="image" src="{{ asset('img/TwitchLogo.png') }}" alt="Submit" 
                    style="max-width: 65px; width: 50vw";
                />
                
            </div>
            
        </div>
        <footer class="absolute bottom-0 w-full p-8 z-30">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="text-white/50 font-mono text-xs tracking-widest uppercase">
                &copy; {{ date('Y') }} TheOneGreenBear
            </div>

            <div class="flex gap-6">
                <a href="#" class="text-white/40 hover:text-white font-mono text-xs transition">Terms</a>
                <a href="#" class="text-white/40 hover:text-white font-mono text-xs transition">Privacy</a>
                <a href="#" class="text-white/40 hover:text-white font-mono text-xs transition">Support</a>
            </div>
            
        </div>
    </footer>
    </div>
    

    

</body>
</html>