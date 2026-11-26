@props([
    // File number, matching the home page tiles (01 Projects, 02 Games, 03 Anime, 04 Spotify).
    'number',
    'title',
    'subtitle' => null,
    // Live stats shown as HUD readouts: [label => value].
    'stats' => [],
])

{{-- Page header in the intel-file style: a numbered file tag that decodes itself, a serif title, a
     tracked subtitle and live stats as HUD readouts, underlined by a leader line that draws itself
     in (decoding: resources/js/Hud.js; styles: "Home hero HUD effects" in app.css). --}}
<header {{ $attributes->merge(['class' => 'mb-10']) }}>
    <p class="flex items-center gap-3 font-mono text-[10px] uppercase tracking-[0.3em] text-ink/70">
        <span class="bg-ink px-1.5 py-0.5 text-paper">{{ $number }}</span>
        <span data-scramble data-scramble-delay="150">{{ __('File') }} // {{ $title }}</span>
        <span class="hud-blink inline-block size-1.5 bg-ink/70" aria-hidden="true"></span>
    </p>

    <div class="mt-5 flex flex-wrap items-end justify-between gap-6">
        <div>
            <h1 class="font-display text-4xl uppercase leading-[0.9] text-ink md:text-6xl">{{ $title }}<span class="text-azuki">.</span></h1>

            @if ($subtitle)
                <p class="mt-4 max-w-md font-mono text-[11px] uppercase tracking-[0.25em] text-ink/60">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($stats)
            <dl class="flex flex-wrap gap-y-3 font-mono">
                @foreach ($stats as $label => $value)
                    <div class="px-4 first:pl-0 not-first:border-l not-first:border-ink/20 md:px-6">
                        <dt class="text-[9px] uppercase tracking-[0.25em] text-ink/50">{{ $label }}</dt>
                        <dd class="mt-1 font-display text-2xl leading-none text-ink md:text-3xl">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>

    {{-- Leader line: a node, a rule that draws itself in, then a pair of square marks. --}}
    <div class="mt-6 flex items-center gap-2" aria-hidden="true">
        <span class="size-2 shrink-0 border border-ink/60"></span>
        <span class="hud-rule h-px flex-1 origin-left bg-ink/40"></span>
        <span class="size-1 shrink-0 bg-ink/60"></span>
        <span class="hud-blink size-1 shrink-0 bg-ink/60" style="--d: 0.8s"></span>
    </div>
</header>
