@props(['repo', 'large' => false, 'index' => 0])

<article style="--i: {{ min($index, 15) }}" class="card-lift group flex flex-col overflow-hidden rounded-md border border-ink/10 bg-sand">
    {{-- Dark banner tinted with the project's language colour, in place of an image. --}}
    <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true"
       class="relative flex {{ $large ? 'aspect-[3/1]' : 'aspect-[5/2]' }} items-center justify-center overflow-hidden border-b border-ink/10"
       style="background: radial-gradient(circle at 15% 20%, {{ $repo->languageColor() }}66, transparent 55%), radial-gradient(circle at 90% 110%, #BB3F3F33, transparent 50%), #EBE7E0;">
        <svg class="size-12 text-ink/15 transition group-hover:text-azuki/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 7l-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>
        </svg>
        <span class="absolute bottom-2 left-3 font-mono text-[10px] text-ink/30">{{ $repo->full_name }}</span>
    </a>

    <div class="flex flex-1 flex-col gap-3 p-4 font-mono">
        <div class="flex items-start justify-between gap-2">
            <h3 class="{{ $large ? 'text-lg' : 'text-sm' }} text-ink">
                <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" class="hover:text-azuki">{{ $repo->displayName() }}</a>
            </h3>
            @if ($repo->is_archived)
                <span class="shrink-0 rounded-sm border border-ink/20 px-1.5 py-0.5 text-[10px] uppercase text-ink/50">{{ __('Archived') }}</span>
            @endif
        </div>

        <p class="text-xs leading-relaxed {{ $repo->description ? 'text-ink/70' : 'italic text-ink/30' }} {{ $large ? '' : 'line-clamp-3' }}">
            {{ $repo->description ?: __('No description yet.') }}
        </p>

        @if ($repo->displayTopics())
            <ul class="flex flex-wrap gap-1.5">
                @foreach ($repo->displayTopics() as $topic)
                    <li class="rounded-full bg-azuki/10 px-2 py-0.5 text-[10px] text-azuki">{{ $topic }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-1 pt-2 text-[10px] text-ink/50">
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
            <a href="{{ $repo->html_url }}" target="_blank" rel="noopener" class="rounded-sm border border-ink/20 px-2 py-1 text-ink/70 transition hover:bg-ink/5 hover:text-ink">{{ __('Code') }}</a>
            @if ($repo->homepage)
                <a href="{{ $repo->homepage }}" target="_blank" rel="noopener" class="rounded-sm bg-azuki px-2 py-1 text-white transition hover:bg-azuki/80">{{ __('Live site') }}</a>
            @endif
        </div>
    </div>
</article>
