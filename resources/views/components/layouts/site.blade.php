<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['forceDark' => true])
    </head>
    {{-- Same framed look as the welcome page: blurred backdrop, bordered window, navbar on top. --}}
    <body class="theme-greenbear relative h-screen w-full overflow-hidden bg-black antialiased">
        <div class="WelcomePagebg-image absolute inset-0 scale-105 blur-lg"></div>

        <div class="WelcomePagebg-image absolute inset-3 md:inset-20"></div>

        <div class="no-scrollbar absolute inset-3 overflow-y-auto border-4 border-white/50 md:inset-20">
            <div class="relative flex min-h-full flex-col">
                <div class="sticky top-0 z-40">
                    <x-navbar />
                </div>

                <main class="flex flex-1 items-center justify-center p-4 md:p-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
