<x-layouts.site :title="__('Spotify')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <div class="mb-8">
            <h1 class="font-display text-3xl uppercase leading-none text-ink md:text-5xl">{{ __('Spotify') }} <span class="text-azuki">//</span></h1>
            <p class="mt-3 font-mono text-[11px] uppercase tracking-[0.25em] text-ink/60">{{ __('What I\'m listening to, live from Spotify.') }}</p>
        </div>

        @if (session('spotify_status'))
            <p class="mb-6 rounded-md border border-azuki/40 bg-azuki/10 p-3 font-mono text-xs text-azuki">{{ session('spotify_status') }}</p>
        @endif
        @if (session('spotify_error'))
            <p class="mb-6 rounded-md border border-red-500/40 bg-red-500/10 p-3 font-mono text-xs text-red-700">{{ session('spotify_error') }}</p>
        @endif

        <!-- Owner-only connection controls -->
        @if ($isOwner)
            <div class="mb-8 flex flex-wrap items-center justify-between gap-3 rounded-md border border-ink/10 bg-ink/5 p-4 font-mono text-xs">
                <p class="text-ink/70">
                    @if (! $configured)
                        {{ __('Add SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET to .env to connect.') }}
                    @elseif ($connected)
                        <span class="text-azuki">&#9679;</span> {{ __('Connected. Only you can see this.') }}
                    @else
                        {{ __('Spotify isn\'t connected. Only you can see this.') }}
                    @endif
                </p>

                @if ($configured)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('spotify.connect') }}" class="rounded-md bg-azuki px-3 py-2 uppercase tracking-wider text-paper transition hover:bg-azuki/80">
                            {{ $connected ? __('Reconnect') : __('Connect Spotify') }}
                        </a>
                        @if ($connected)
                            <form method="POST" action="{{ route('spotify.disconnect') }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-ink/20 px-3 py-2 uppercase tracking-wider text-ink/70 transition hover:bg-ink/5 hover:text-ink">{{ __('Disconnect') }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if (empty($data))
            <div class="rounded-md border border-dashed border-ink/20 p-10 text-center font-mono">
                <p class="text-ink">{{ __('Spotify isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-ink/50">{{ __('Music will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Now playing / last played -->
            @if ($listening)
                @php($track = $listening['track'])
                <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="group mb-10 flex items-center gap-5 rounded-md border border-ink/10 bg-sand p-4 transition hover:border-azuki">
                    @if ($track['image'])
                        <span class="dither block size-24 shrink-0 rounded-sm md:size-32">
                            <img src="{{ $track['image'] }}" alt="" class="h-full w-full object-cover">
                        </span>
                    @endif
                    <div class="min-w-0 font-mono">
                        <p class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-azuki">
                            @if ($listening['playing'])
                                <span class="flex h-3 items-end gap-0.5" aria-hidden="true">
                                    <span class="w-0.5 animate-pulse bg-azuki" style="height: 60%"></span>
                                    <span class="w-0.5 animate-pulse bg-azuki" style="height: 100%; animation-delay: 150ms"></span>
                                    <span class="w-0.5 animate-pulse bg-azuki" style="height: 40%; animation-delay: 300ms"></span>
                                </span>
                            @endif
                            {{ __($listening['label']) }}
                        </p>
                        <p class="mt-2 truncate text-lg text-ink md:text-xl">{{ $track['name'] }}</p>
                        <p class="truncate text-sm text-ink/70">{{ $track['artists'] }}</p>
                        @if ($track['album'])
                            <p class="truncate text-xs text-ink/40">{{ $track['album'] }}</p>
                        @endif
                    </div>
                </a>
            @endif

            <div class="grid gap-10 lg:grid-cols-2">
                <!-- Top tracks -->
                <section>
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Top tracks this month') }}</h2>
                    @forelse ($data['top_tracks'] ?? [] as $track)
                        <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-md p-2 font-mono transition hover:bg-ink/5">
                            <span class="w-5 shrink-0 text-right text-xs text-ink/40">{{ $loop->iteration }}</span>
                            @if ($track['image'])
                                <img src="{{ $track['image'] }}" alt="" loading="lazy" class="size-10 shrink-0 rounded-sm object-cover">
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate text-sm text-ink">{{ $track['name'] }}</span>
                                <span class="block truncate text-xs text-ink/50">{{ $track['artists'] }}</span>
                            </span>
                        </a>
                    @empty
                        <p class="font-mono text-xs text-ink/50">{{ __('Not enough listening yet.') }}</p>
                    @endforelse
                </section>

                <!-- Top artists -->
                <section>
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Top artists this month') }}</h2>
                    <div class="grid grid-cols-3 gap-4 sm:grid-cols-5 lg:grid-cols-3 xl:grid-cols-5">
                        @forelse ($data['top_artists'] ?? [] as $artist)
                            <a href="{{ $artist['url'] }}" target="_blank" rel="noopener" class="group text-center font-mono">
                                <span class="dither mx-auto block aspect-square w-full overflow-hidden rounded-full border border-ink/10 bg-ink/5 transition group-hover:border-azuki">
                                    @if ($artist['image'])
                                        <img src="{{ $artist['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-ink">{{ $artist['name'] }}</span>
                            </a>
                        @empty
                            <p class="col-span-full font-mono text-xs text-ink/50">{{ __('Not enough listening yet.') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <!-- Recently played -->
            @if (! empty($data['recent']))
                <section class="mt-10">
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Recently played') }}</h2>
                    <div class="no-scrollbar flex gap-4 overflow-x-auto pb-2">
                        @foreach ($data['recent'] as $track)
                            <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="w-32 shrink-0 font-mono">
                                <span class="dither block aspect-square overflow-hidden rounded-sm bg-ink/5">
                                    @if ($track['image'])
                                        <img src="{{ $track['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-ink">{{ $track['name'] }}</span>
                                <span class="block truncate text-[10px] text-ink/50">{{ $track['artists'] }}</span>
                                @if ($track['played_at'])
                                    <span class="block text-[10px] text-ink/30">{{ \Illuminate\Support\Carbon::parse($track['played_at'])->diffForHumans() }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Playlists -->
            @if (! empty($data['playlists']))
                <section class="mt-10">
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('My playlists') }}</h2>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($data['playlists'] as $playlist)
                            <a href="{{ $playlist['url'] }}" target="_blank" rel="noopener" class="group font-mono">
                                <span class="dither block aspect-square overflow-hidden rounded-md border border-ink/10 bg-ink/5 transition group-hover:border-azuki">
                                    @if ($playlist['image'])
                                        <img src="{{ $playlist['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-ink">{{ $playlist['name'] }}</span>
                                @if ($playlist['tracks'])
                                    <span class="block text-[10px] text-ink/50">{{ trans_choice(':count track|:count tracks', $playlist['tracks']) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-ink/40">
                    {{ __('Updated :time', ['time' => $lastSynced->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
