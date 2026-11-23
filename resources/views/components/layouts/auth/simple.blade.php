<x-layouts.site :title="$title ?? null" centered>
    <x-glass-panel class="w-full max-w-md rounded-sm border border-ink/10 bg-sand p-8 shadow-lg shadow-ink/5">
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
