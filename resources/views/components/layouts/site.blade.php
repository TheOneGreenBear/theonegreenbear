@props([
    'title' => null,
    // Centre the content vertically (single forms like login). Content pages align to the top, so their
    // heading and tabs stay put when the content below changes height.
    'centered' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['forceDark' => true])
    </head>
    {{-- Same framed look as the home page: a fog-paper window over the drifting fog, navbar on top, grain over everything. --}}
    <body class="theme-greenbear grain relative h-screen w-full overflow-hidden bg-sand text-ink antialiased">
        {{-- Fog backdrop behind the window (clipped so it can never widen the page). --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="bg-fog absolute inset-0"></div>
        </div>

        <div class="no-scrollbar absolute inset-3 overflow-y-auto border border-ink/15 bg-paper window-frame md:inset-10 lg:inset-16">
            <div class="relative flex min-h-full flex-col">
                <div class="sticky top-0 z-40">
                    <x-navbar />
                </div>

                <main class="flex flex-1 justify-center p-4 md:p-10 {{ $centered ? 'items-center' : 'items-start' }}">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
