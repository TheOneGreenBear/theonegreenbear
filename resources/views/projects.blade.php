<x-layouts.site :title="__('Projects')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-mono text-2xl uppercase tracking-widest text-green-500">{{ __('Projects') }}</h1>
                <p class="mt-2 font-mono text-xs text-white/60">{{ __('Things I\'ve built, live from GitHub.') }}</p>
            </div>

            @if ($total)
                <dl class="flex flex-wrap gap-3 font-mono">
                    <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Projects') }}</dt>
                        <dd class="text-lg text-white">{{ $total }}</dd>
                    </div>
                    <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Languages') }}</dt>
                        <dd class="text-lg text-white">{{ $languages->count() }}</dd>
                    </div>
                    @if ($stars)
                        <div class="rounded-md border border-white/10 bg-white/5 px-4 py-2">
                            <dt class="text-[10px] uppercase tracking-wider text-white/50">{{ __('Stars') }}</dt>
                            <dd class="text-lg text-white">{{ $stars }}</dd>
                        </div>
                    @endif
                </dl>
            @endif
        </div>

        @if (! $total)
            <div class="rounded-md border border-dashed border-white/20 p-10 text-center font-mono">
                <p class="text-white">{{ __('GitHub isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-white/50">{{ __('Projects will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Language filter -->
            @if ($languages->count() > 1)
                <nav class="no-scrollbar mb-8 flex gap-1 overflow-x-auto" aria-label="{{ __('Filter by language') }}">
                    <a href="{{ route('projects') }}" @if (! $language) aria-current="page" @endif
                       class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ ! $language ? 'bg-azukigray-400 text-[#09090b]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        {{ __('All') }} <span class="{{ ! $language ? 'text-[#09090b]/60' : 'text-white/40' }}">{{ $total }}</span>
                    </a>
                    @foreach ($languages as $name => $count)
                        @php($active = $name === $language)
                        <a href="{{ route('projects', ['language' => $name]) }}" @if ($active) aria-current="page" @endif
                           class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ $active ? 'bg-azukigray-400 text-[#09090b]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                            {{ $name }} <span class="{{ $active ? 'text-[#09090b]/60' : 'text-white/40' }}">{{ $count }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif

            @if ($featured->isNotEmpty())
                <section class="mb-10">
                    <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('Featured') }}</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($featured as $repo)
                            <x-project-card :repo="$repo" large />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($projects->isNotEmpty())
                <section>
                    @if ($featured->isNotEmpty())
                        <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-white">{{ __('More projects') }}</h2>
                    @endif
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($projects as $repo)
                            <x-project-card :repo="$repo" />
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
