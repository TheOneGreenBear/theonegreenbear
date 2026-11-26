@php use App\Models\AnimeEntry; @endphp

<x-layouts.site :title="__('Anime')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <x-file-header
            number="03"
            :title="__('Anime')"
            :subtitle="__('What I\'m watching and what I\'ve watched, live from MyAnimeList.')"
            :stats="$total ? array_filter([
                __('Completed') => number_format($counts['completed'] ?? 0),
                __('Episodes') => number_format($episodesWatched),
                __('Avg. score') => $averageScore ? number_format($averageScore, 1) : null,
            ], fn ($value) => $value !== null) : []"
        />

        @if (! $total)
            <div class="rounded-md border border-dashed border-ink/20 p-10 text-center font-mono">
                <p class="text-ink">{{ __('MyAnimeList isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-ink/50">{{ __('The list will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Status tabs: links work without JavaScript; Tabs.js switches panels in place without a reload. -->
            <div data-tabs="status"
                 data-tab-active="bg-ink text-paper"
                 data-tab-idle="text-ink/70 hover:bg-ink/5 hover:text-ink">
                <nav class="no-scrollbar mb-6 flex gap-1 overflow-x-auto" aria-label="{{ __('List status') }}">
                    @foreach (AnimeEntry::STATUSES as $key => $label)
                        @php($active = $key === $status)
                        <a
                            href="{{ route('anime', ['status' => $key]) }}"
                            data-tab="{{ $key }}"
                            @if ($active) aria-current="page" @endif
                            class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ $active ? 'bg-ink text-paper' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}"
                        >
                            {{ __($label) }}
                            <span class="opacity-60">{{ $counts[$key] }}</span>
                        </a>
                    @endforeach
                </nav>

                @foreach ($groups as $key => $entries)
                    <div data-tab-panel="{{ $key }}" @if ($key !== $status) hidden @endif>
                        @if ($entries->isEmpty())
                            <p class="font-mono text-xs text-ink/50">{{ __('Nothing here yet.') }}</p>
                        @else
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                @foreach ($entries as $entry)
                                    <a href="{{ $entry->malUrl() }}" target="_blank" rel="noopener" style="--i: {{ min($loop->index, 15) }}" class="card-lift group flex flex-col overflow-hidden rounded-md border border-ink/10 bg-sand">
                                        <div class="dither relative aspect-[225/320] bg-ink/5">
                                            @if ($entry->image_url)
                                                <img src="{{ $entry->image_url }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover" onerror="this.style.display='none'">
                                            @endif

                                            @if ($entry->score)
                                                <span class="absolute top-2 right-2 rounded-sm bg-paper px-1.5 py-0.5 font-mono text-xs text-azuki" title="{{ __('My score') }}">&#9733; {{ $entry->score }}</span>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col gap-1 p-3 font-mono">
                                            <p class="line-clamp-2 text-xs text-ink">{{ $entry->title }}</p>

                                            @if (in_array($entry->status, ['watching', 'on_hold', 'dropped']))
                                                <p class="mt-auto text-[10px] text-ink/50">
                                                    {{ __('Ep :watched / :total', ['watched' => $entry->episodes_watched, 'total' => $entry->num_episodes ?: '?']) }}
                                                </p>
                                                @if (! is_null($entry->progressPercent()))
                                                    <span class="block h-1 rounded-full bg-ink/10">
                                                        <span class="block h-1 rounded-full bg-azuki" style="width: {{ $entry->progressPercent() }}%"></span>
                                                    </span>
                                                @endif
                                            @else
                                                <p class="mt-auto text-[10px] uppercase text-ink/40">
                                                    {{ $entry->media_type }}@if ($entry->num_episodes) &middot; {{ $entry->num_episodes }} {{ __('eps') }}@endif
                                                </p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-ink/40">
                    {{ __('Updated :time', ['time' => \Illuminate\Support\Carbon::parse($lastSynced)->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
