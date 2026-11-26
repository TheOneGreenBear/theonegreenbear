@php use App\Models\SteamGame; @endphp

<x-layouts.site :title="__('Games')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <x-file-header
            number="02"
            :title="__('Games')"
            :subtitle="__('What I\'ve been playing, live from Steam.')"
            :stats="$gameCount ? [
                __('Games') => number_format($gameCount),
                __('Hours played') => number_format($totalMinutes / 60),
            ] : []"
        />

        @if (! $gameCount)
            <div class="rounded-md border border-dashed border-ink/20 p-10 text-center font-mono">
                <p class="text-ink">{{ __('Steam isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-ink/50">{{ __('Games will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Recently played -->
            <section>
                <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Last two weeks') }}</h2>

                @if ($recent->isEmpty())
                    <p class="font-mono text-xs text-ink/50">{{ __('Nothing played in the last two weeks.') }}</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($recent as $game)
                            <a href="{{ $game->storeUrl() }}" target="_blank" rel="noopener" class="card-lift group overflow-hidden rounded-md border border-ink/10 bg-sand">
                                {{-- Sand placeholder keeps the card shape when Steam has no header image. --}}
                                <div class="dither aspect-[460/215] w-full bg-sand-dark">
                                    <img src="{{ $game->headerImageUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover" onerror="this.style.visibility='hidden'">
                                </div>
                                <div class="p-3 font-mono">
                                    <p class="truncate text-sm text-ink">{{ $game->name }}</p>
                                    <p class="mt-1 text-xs text-azuki">{{ SteamGame::hours($game->playtime_2weeks) }} {{ __('h last 2 weeks') }}</p>
                                    <p class="text-xs text-ink/50">{{ SteamGame::hours($game->playtime_forever) }} {{ __('h total') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Most played -->
            <section class="mt-10">
                <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Most played') }}</h2>

                <ol class="space-y-2">
                    @foreach ($mostPlayed as $game)
                        <li>
                            <a href="{{ $game->storeUrl() }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-md p-2 font-mono transition hover:bg-ink/5">
                                <span class="w-6 shrink-0 text-right text-xs text-ink/40">{{ $loop->iteration }}</span>
                                <img src="{{ $game->headerImageUrl() }}" alt="" loading="lazy" class="hidden h-9 w-[77px] shrink-0 rounded-sm object-cover sm:block" onerror="this.style.visibility='hidden'">
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm text-ink">{{ $game->name }}</span>
                                    <span class="mt-1 block h-1 rounded-full bg-ink/10">
                                        <span class="block h-1 rounded-full bg-azuki" style="width: {{ round($game->playtime_forever / $maxMinutes * 100, 1) }}%"></span>
                                    </span>
                                </span>
                                <span class="shrink-0 text-xs text-ink/70">{{ number_format($game->playtime_forever / 60) }} h</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </section>

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-ink/40">
                    {{ __('Updated :time', ['time' => \Illuminate\Support\Carbon::parse($lastSynced)->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
