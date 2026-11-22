@php use App\Models\AnimeEntry; @endphp

<x-layouts.site :title="__('Anime')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-mono text-2xl uppercase tracking-widest text-green-500">{{ __('Anime') }}</h1>
                <p class="mt-2 font-mono text-xs text-white/60">{{ __('What I\'m watching and what I\'ve watched, live from MyAnimeList.') }}</p>
            </div>

            @if ($total)
                <dl class="flex flex-wrap gap-3 font-mono">
                    <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Completed') }}</dt>
                        <dd class="text-lg text-white">{{ number_format($counts['completed'] ?? 0) }}</dd>
                    </div>
                    <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Episodes') }}</dt>
                        <dd class="text-lg text-white">{{ number_format($episodesWatched) }}</dd>
                    </div>
                    @if ($averageScore)
                        <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                            <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Avg. score') }}</dt>
                            <dd class="text-lg text-white">{{ number_format($averageScore, 1) }}</dd>
                        </div>
                    @endif
                </dl>
            @endif
        </div>

        @if (! $total)
            <div class="rounded-md border border-dashed border-white/20 p-10 text-center font-mono">
                <p class="text-white">{{ __('MyAnimeList isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-white/50">{{ __('The list will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Status tabs -->
            <nav class="no-scrollbar mb-6 flex gap-1 overflow-x-auto" aria-label="{{ __('List status') }}">
                @foreach (AnimeEntry::STATUSES as $key => $label)
                    @php($active = $key === $status)
                    <a
                        href="{{ route('anime', ['status' => $key]) }}"
                        @if ($active) aria-current="page" @endif
                        class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ $active ? 'bg-azukigray-400 text-[#09090b]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}"
                    >
                        {{ __($label) }}
                        <span class="{{ $active ? 'text-[#09090b]/60' : 'text-white/40' }}">{{ $counts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </nav>

            @if ($entries->isEmpty())
                <p class="font-mono text-xs text-white/50">{{ __('Nothing here yet.') }}</p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    @foreach ($entries as $entry)
                        <a href="{{ $entry->malUrl() }}" target="_blank" rel="noopener" class="group flex flex-col overflow-hidden rounded-md border border-white/10 bg-[#09090b] transition hover:border-[#08a88a]">
                            <div class="relative aspect-[225/320] bg-white/5">
                                @if ($entry->image_url)
                                    <img src="{{ $entry->image_url }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition group-hover:opacity-90" onerror="this.style.display='none'">
                                @endif

                                @if ($entry->score)
                                    <span class="absolute top-2 right-2 rounded-sm bg-[#09090b]/90 px-1.5 py-0.5 font-mono text-xs text-[#08a88a]" title="{{ __('My score') }}">&#9733; {{ $entry->score }}</span>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col gap-1 p-3 font-mono">
                                <p class="line-clamp-2 text-xs text-white">{{ $entry->title }}</p>

                                @if (in_array($entry->status, ['watching', 'on_hold', 'dropped']))
                                    <p class="mt-auto text-[10px] text-white/50">
                                        {{ __('Ep :watched / :total', ['watched' => $entry->episodes_watched, 'total' => $entry->num_episodes ?: '?']) }}
                                    </p>
                                    @if (! is_null($entry->progressPercent()))
                                        <span class="block h-1 rounded-full bg-white/10">
                                            <span class="block h-1 rounded-full bg-[#08a88a]" style="width: {{ $entry->progressPercent() }}%"></span>
                                        </span>
                                    @endif
                                @else
                                    <p class="mt-auto text-[10px] uppercase text-white/40">
                                        {{ $entry->media_type }}@if ($entry->num_episodes) &middot; {{ $entry->num_episodes }} {{ __('eps') }}@endif
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-white/40">
                    {{ __('Updated :time', ['time' => \Illuminate\Support\Carbon::parse($lastSynced)->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
