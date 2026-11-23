<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['forceDark' => true])
    </head>
    {{-- Same framed look as the home page: a white "paper" window over the wave pattern, navbar on top. --}}
    <body class="theme-greenbear relative h-screen w-full overflow-hidden bg-sand text-ink antialiased">
        {{-- Wave-pattern backdrop behind the window (clipped so it can never widen the page). --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="bg-seigaiha absolute inset-0"></div>
        </div>

        <div class="no-scrollbar absolute inset-3 overflow-y-auto border border-ink/15 bg-paper shadow-xl shadow-ink/15 md:inset-10 lg:inset-16">
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
