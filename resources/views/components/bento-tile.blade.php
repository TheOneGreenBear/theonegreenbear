@props([
    'number',
    'label',
    'href' => null,
    'status' => null,
    'accent' => false,
])

@php
    $tag = $href ? 'a' : 'div';
    $surface = $accent
        ? 'bg-[#08a88a] text-[#09090b]'
        : 'bg-azukigray-900/90 text-white hover:bg-azukigray-800';
@endphp

{{-- A numbered grid tile: big faded number, widely spaced label, live status line, line-art icon in the corner. --}}
<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "group relative flex min-h-36 flex-col justify-end overflow-hidden rounded-sm border border-white/5 p-4 transition md:p-5 duration-300 {$surface}"]) }}
>
    @isset($icon)
        <div class="pointer-events-none absolute top-3 right-3 size-10 transition duration-500 md:top-4 md:right-4 md:size-28 {{ $accent ? 'text-[#09090b]/40' : 'text-[#08a88a]/35 group-hover:-translate-y-1 group-hover:text-[#08a88a]/80' }}">
            {{ $icon }}
        </div>
    @endisset

    <span class="relative font-display text-4xl leading-none transition-colors duration-300 md:text-5xl {{ $accent ? 'text-[#09090b]/50' : 'text-white/20 group-hover:text-[#08a88a]' }}">{{ $number }}</span>
    <span class="relative mt-2 font-mono text-xs font-bold uppercase tracking-[0.15em] md:tracking-[0.3em]">{{ $label }}</span>

    @if ($status)
        <span class="relative mt-2 truncate font-mono text-[10px] {{ $accent ? 'text-[#09090b]/70' : 'text-white/50' }}">{{ $status }}</span>
    @endif

    {{ $slot }}
</{{ $tag }}>
