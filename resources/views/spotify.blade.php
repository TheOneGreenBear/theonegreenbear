<x-layouts.site :title="__('Spotify')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <div class="mb-8">
            <h1 class="font-mono text-2xl uppercase tracking-widest text-green-500">{{ __('Spotify') }}</h1>
            <p class="mt-2 font-mono text-xs text-white/60">{{ __('What I\'m listening to, live from Spotify.') }}</p>
        </div>

        @if (session('spotify_status'))
            <p class="mb-6 rounded-md border border-[#08a88a]/40 bg-[#08a88a]/10 p-3 font-mono text-xs text-[#08a88a]">{{ session('spotify_status') }}</p>
        @endif
        @if (session('spotify_error'))
            <p class="mb-6 rounded-md border border-red-500/40 bg-red-500/10 p-3 font-mono text-xs text-red-400">{{ session('spotify_error') }}</p>
        @endif

        <!-- Owner-only connection controls -->
        @if ($isOwner)
            <div class="mb-8 flex flex-wrap items-center justify-between gap-3 rounded-md border border-white/10 bg-white/5 p-4 font-mono text-xs">
                <p class="text-white/70">
                    @if (! $configured)
                        {{ __('Add SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET to .env to connect.') }}
                    @elseif ($connected)
                        <span class="text-[#08a88a]">&#9679;</span> {{ __('Connected. Only you can see this.') }}
                    @else
                        {{ __('Spotify isn\'t connected. Only you can see this.') }}
                    @endif
                </p>

                @if ($configured)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('spotify.connect') }}" class="rounded-md bg-[#08a88a] px-3 py-2 uppercase tracking-wider text-[#09090b] transition hover:bg-[#08a88a]/80">
                            {{ $connected ? __('Reconnect') : __('Connect Spotify') }}
                        </a>
                        @if ($connected)
                            <form method="POST" action="{{ route('spotify.disconnect') }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-white/20 px-3 py-2 uppercase tracking-wider text-white/70 transition hover:bg-white/5 hover:text-white">{{ __('Disconnect') }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if (empty($data))
            <div class="rounded-md border border-dashed border-white/20 p-10 text-center font-mono">
                <p class="text-white">{{ __('Spotify isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-white/50">{{ __('Music will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Now playing / last played -->
            @if ($listening)
                @php($track = $listening['track'])
                <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="group mb-10 flex items-center gap-5 rounded-md border border-white/10 bg-[#09090b] p-4 transition hover:border-[#08a88a]">
                    @if ($track['image'])
                        <img src="{{ $track['image'] }}" alt="" class="size-24 shrink-0 rounded-sm object-cover md:size-32">
                    @endif
                    <div class="min-w-0 font-mono">
                        <p class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-[#08a88a]">
                            @if ($listening['playing'])
                                <span class="flex h-3 items-end gap-0.5" aria-hidden="true">
                                    <span class="w-0.5 animate-pulse bg-[#08a88a]" style="height: 60%"></span>
                                    <span class="w-0.5 animate-pulse bg-[#08a88a]" style="height: 100%; animation-delay: 150ms"></span>
                                    <span class="w-0.5 animate-pulse bg-[#08a88a]" style="height: 40%; animation-delay: 300ms"></span>
                                </span>
                            @endif
                            {{ __($listening['label']) }}
                        </p>
                        <p class="mt-2 truncate text-lg text-white md:text-xl">{{ $track['name'] }}</p>
                        <p class="truncate text-sm text-white/70">{{ $track['artists'] }}</p>
                        @if ($track['album'])
                            <p class="truncate text-xs text-white/40">{{ $track['album'] }}</p>
                        @endif
                    </div>
                </a>
            @endif

            <div class="grid gap-10 lg:grid-cols-2">
                <!-- Top tracks -->
                <section>
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('Top tracks this month') }}</h2>
                    @forelse ($data['top_tracks'] ?? [] as $track)
                        <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-md p-2 font-mono transition hover:bg-white/5">
                            <span class="w-5 shrink-0 text-right text-xs text-white/40">{{ $loop->iteration }}</span>
                            @if ($track['image'])
                                <img src="{{ $track['image'] }}" alt="" loading="lazy" class="size-10 shrink-0 rounded-sm object-cover">
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate text-sm text-white">{{ $track['name'] }}</span>
                                <span class="block truncate text-xs text-white/50">{{ $track['artists'] }}</span>
                            </span>
                        </a>
                    @empty
                        <p class="font-mono text-xs text-white/50">{{ __('Not enough listening yet.') }}</p>
                    @endforelse
                </section>

                <!-- Top artists -->
                <section>
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('Top artists this month') }}</h2>
                    <div class="grid grid-cols-3 gap-4 sm:grid-cols-5 lg:grid-cols-3 xl:grid-cols-5">
                        @forelse ($data['top_artists'] ?? [] as $artist)
                            <a href="{{ $artist['url'] }}" target="_blank" rel="noopener" class="group text-center font-mono">
                                <span class="mx-auto block aspect-square w-full overflow-hidden rounded-full border border-white/10 bg-white/5 transition group-hover:border-[#08a88a]">
                                    @if ($artist['image'])
                                        <img src="{{ $artist['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-white">{{ $artist['name'] }}</span>
                            </a>
                        @empty
                            <p class="col-span-full font-mono text-xs text-white/50">{{ __('Not enough listening yet.') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <!-- Recently played -->
            @if (! empty($data['recent']))
                <section class="mt-10">
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('Recently played') }}</h2>
                    <div class="no-scrollbar flex gap-4 overflow-x-auto pb-2">
                        @foreach ($data['recent'] as $track)
                            <a href="{{ $track['url'] }}" target="_blank" rel="noopener" class="w-32 shrink-0 font-mono">
                                <span class="block aspect-square overflow-hidden rounded-sm bg-white/5">
                                    @if ($track['image'])
                                        <img src="{{ $track['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover transition hover:opacity-80">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-white">{{ $track['name'] }}</span>
                                <span class="block truncate text-[10px] text-white/50">{{ $track['artists'] }}</span>
                                @if ($track['played_at'])
                                    <span class="block text-[10px] text-white/30">{{ \Illuminate\Support\Carbon::parse($track['played_at'])->diffForHumans() }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Playlists -->
            @if (! empty($data['playlists']))
                <section class="mt-10">
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('My playlists') }}</h2>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($data['playlists'] as $playlist)
                            <a href="{{ $playlist['url'] }}" target="_blank" rel="noopener" class="group font-mono">
                                <span class="block aspect-square overflow-hidden rounded-md border border-white/10 bg-white/5 transition group-hover:border-[#08a88a]">
                                    @if ($playlist['image'])
                                        <img src="{{ $playlist['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @endif
                                </span>
                                <span class="mt-2 block truncate text-xs text-white">{{ $playlist['name'] }}</span>
                                @if ($playlist['tracks'])
                                    <span class="block text-[10px] text-white/50">{{ trans_choice(':count track|:count tracks', $playlist['tracks']) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-white/40">
                    {{ __('Updated :time', ['time' => $lastSynced->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
