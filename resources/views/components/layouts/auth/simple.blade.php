<x-layouts.site :title="$title ?? null">
    <x-glass-panel class="w-full max-w-md p-8">
        <a href="{{ route('home') }}" class="mx-auto mb-2 block w-fit transition hover:scale-105">
            <img
                src="{{ asset('img/TheOneGreenBearLogo.png') }}"
                alt="{{ config('app.name') }}"
                class="w-24 drop-shadow-[0_10px_25px_rgba(0,0,0,0.5)]"
            >
        </a>

        <div class="flex flex-col gap-6">
            {{ $slot }}
        </div>
    </x-glass-panel>
</x-layouts.site>
