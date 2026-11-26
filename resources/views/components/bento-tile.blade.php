@props([
    'number',
    'label',
    'href' => null,
    'status' => null,
    'accent' => false,
])

@php
    $tag = $href ? 'a' : 'div';

    // Hover: plain tiles get a red edge and red number/icon; the red accent tile deepens. Both lift.
    $surface = $accent
        ? 'bg-azuki text-paper hover:bg-azuki-dark'
        : 'bg-sand text-ink hover:border-azuki/60 hover:bg-sand-dark';
@endphp

{{-- A numbered grid tile: big faded number, widely spaced label, live status line, line-art icon in the corner. --}}
<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "group/tile relative flex min-h-36 flex-col justify-end overflow-hidden rounded-sm border border-ink/5 p-4 transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_14px_30px_-14px_rgb(20_20_20/0.45)] motion-reduce:hover:translate-y-0 md:p-5 {$surface}"]) }}
>
    @isset($icon)
        <div class="pointer-events-none absolute top-3 right-3 size-10 transition duration-500 md:top-4 md:right-4 md:size-28 {{ $accent ? 'text-paper/40' : 'text-ink/25 group-hover/tile:-translate-y-1 group-hover/tile:text-azuki' }}">
            {{ $icon }}
        </div>
    @endisset

    <span class="relative font-display text-4xl leading-none transition-colors duration-300 md:text-5xl {{ $accent ? 'text-paper/60 group-hover/tile:text-paper/80' : 'text-ink/40 group-hover/tile:text-azuki' }}">{{ $number }}</span>
    <span class="relative mt-2 font-mono text-xs font-bold uppercase tracking-[0.15em] md:tracking-[0.3em]">{{ $label }}</span>

    @if ($status)
        <span class="relative mt-2 truncate font-mono text-[10px] transition-colors duration-300 {{ $accent ? 'text-paper/70' : 'text-ink/50 group-hover/tile:text-ink/70' }}">{{ $status }}</span>
    @endif

    {{ $slot }}
</{{ $tag }}>
