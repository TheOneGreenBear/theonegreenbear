@props(['repo', 'large' => false])

<article class="group flex flex-col overflow-hidden rounded-md border border-white/10 bg-[#09090b] transition hover:border-[#08a88a]">
    {{-- Dark banner tinted with the project's language colour, in place of an image. --}}
    <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true"
       class="relative flex {{ $large ? 'aspect-[3/1]' : 'aspect-[5/2]' }} items-center justify-center overflow-hidden border-b border-white/10"
       style="background: radial-gradient(circle at 15% 20%, {{ $repo->languageColor() }}66, transparent 55%), radial-gradient(circle at 90% 110%, #08a88a33, transparent 50%), #0d1117;">
        <svg class="size-12 text-white/15 transition group-hover:text-[#08a88a]/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 7l-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>
        </svg>
        <span class="absolute bottom-2 left-3 font-mono text-[10px] text-white/30">{{ $repo->full_name }}</span>
    </a>

    <div class="flex flex-1 flex-col gap-3 p-4 font-mono">
        <div class="flex items-start justify-between gap-2">
            <h3 class="{{ $large ? 'text-lg' : 'text-sm' }} text-white">
                <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" class="hover:text-[#08a88a]">{{ $repo->displayName() }}</a>
            </h3>
            @if ($repo->is_archived)
                <span class="shrink-0 rounded-sm border border-white/20 px-1.5 py-0.5 text-[10px] uppercase text-white/50">{{ __('Archived') }}</span>
            @endif
        </div>

        <p class="text-xs leading-relaxed {{ $repo->description ? 'text-white/70' : 'italic text-white/30' }} {{ $large ? '' : 'line-clamp-3' }}">
            {{ $repo->description ?: __('No description yet.') }}
        </p>

        @if ($repo->displayTopics())
            <ul class="flex flex-wrap gap-1.5">
                @foreach ($repo->displayTopics() as $topic)
                    <li class="rounded-full bg-[#08a88a]/10 px-2 py-0.5 text-[10px] text-[#08a88a]">{{ $topic }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-1 pt-2 text-[10px] text-white/50">
            @if ($repo->language)
                <span class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full" style="background-color: {{ $repo->languageColor() }}"></span>
                    {{ $repo->language }}
                </span>
            @endif
            @if ($repo->stars)
                <span>&#9733; {{ $repo->stars }}</span>
            @endif
            @if ($repo->pushed_at)
                <span>{{ __('Updated :time', ['time' => $repo->pushed_at->diffForHumans()]) }}</span>
            @endif
        </div>

        <div class="flex gap-2 text-[10px] uppercase tracking-wider">
            <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" class="rounded-sm border border-white/20 px-2 py-1 text-white/70 transition hover:bg-white/5 hover:text-white">{{ __('Code') }}</a>
            @if ($repo->homepage)
                <a href="{{ $repo->homepage }}" target="_blank" rel="noopener" class="rounded-sm bg-[#08a88a] px-2 py-1 text-[#09090b] transition hover:bg-[#08a88a]/80">{{ __('Live site') }}</a>
            @endif
        </div>
    </div>
</article>
