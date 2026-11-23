@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-2 text-center">
    <h1 class="font-display text-2xl uppercase leading-tight text-ink">{{ $title }}</h1>
    <p class="font-mono text-xs leading-relaxed text-ink/70">{{ $description }}</p>
</div>
