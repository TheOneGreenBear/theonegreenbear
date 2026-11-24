<x-layouts.app :title="__('Dashboard')">
    <div class="flex w-full flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Data syncs run on a schedule. Use Sync now to pull the latest straight away.') }}</flux:text>
        </div>

        @if (session('sync_status'))
            <flux:callout variant="success" icon="check-circle" :heading="session('sync_status')" />
        @endif
        @if (session('sync_error'))
            <flux:callout variant="danger" icon="x-circle" :heading="session('sync_error')" />
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($providers as $provider)
                @php
                    $lastRun = $provider['lastRun'];
                    $lastSuccess = $provider['lastSuccess'];
                    $needsConnect = $provider['name'] === 'spotify' && $provider['configured'] && ! $spotifyConnected;
                @endphp

                <section class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700" data-provider="{{ $provider['name'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <flux:heading size="lg" level="2">{{ $provider['label'] }}</flux:heading>

                        @if (! $provider['configured'])
                            <flux:badge size="sm" color="zinc">{{ __('Not set up') }}</flux:badge>
                        @elseif ($needsConnect)
                            <flux:badge size="sm" color="yellow">{{ __('Not connected') }}</flux:badge>
                        @elseif (! $lastRun)
                            <flux:badge size="sm" color="zinc">{{ __('Never synced') }}</flux:badge>
                        @elseif ($lastRun->failed())
                            <flux:badge size="sm" color="red" icon="x-circle">{{ __('Failed') }}</flux:badge>
                        @elseif ($lastRun->succeeded())
                            <flux:badge size="sm" color="green" icon="check-circle">{{ __('OK') }}</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc">{{ __('Skipped') }}</flux:badge>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Last run') }}</dt>
                        <dd>
                            @if ($lastRun)
                                <time datetime="{{ $lastRun->created_at->toIso8601String() }}" title="{{ $lastRun->created_at->toDayDateTimeString() }}">{{ $lastRun->created_at->diffForHumans() }}</time>
                            @else
                                &mdash;
                            @endif
                        </dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Last success') }}</dt>
                        <dd>
                            @if ($lastSuccess)
                                <time datetime="{{ $lastSuccess->created_at->toIso8601String() }}" title="{{ $lastSuccess->created_at->toDayDateTimeString() }}">{{ $lastSuccess->created_at->diffForHumans() }}</time>
                            @else
                                &mdash;
                            @endif
                        </dd>

                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Items') }}</dt>
                        <dd>{{ $lastSuccess ? number_format($lastSuccess->items) : '—' }}</dd>
                    </dl>

                    @if ($lastRun?->failed())
                        <p class="rounded-md bg-red-50 p-3 font-mono text-xs break-words text-red-700 dark:bg-red-500/10 dark:text-red-300">{{ $lastRun->error }}</p>
                    @endif

                    <div class="mt-auto flex flex-wrap gap-2">
                        @if ($needsConnect)
                            <flux:button size="sm" variant="primary" :href="route('spotify.connect')">{{ __('Connect Spotify') }}</flux:button>
                        @elseif ($provider['configured'])
                            <form method="POST" action="{{ route('dashboard.sync', $provider['name']) }}">
                                @csrf
                                <flux:button size="sm" variant="primary" type="submit" icon="arrow-path">{{ __('Sync now') }}</flux:button>
                            </form>
                        @else
                            <flux:text size="sm">{{ __('Add its keys to .env to turn this sync on.') }}</flux:text>
                        @endif
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
