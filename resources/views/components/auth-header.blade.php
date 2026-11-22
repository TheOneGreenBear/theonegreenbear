@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-2 text-center">
    <h1 class="font-mono text-xl uppercase tracking-widest text-green-500">{{ $title }}</h1>
    <p class="font-mono text-xs leading-relaxed text-white/70">{{ $description }}</p>
</div>
