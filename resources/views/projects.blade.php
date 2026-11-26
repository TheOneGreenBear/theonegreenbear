<x-layouts.site :title="__('Projects')">
    <x-glass-panel class="w-full max-w-6xl p-6 md:p-10">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl uppercase leading-none text-ink md:text-5xl">{{ __('Projects') }} <span class="text-azuki">//</span></h1>
                <p class="mt-3 font-mono text-[11px] uppercase tracking-[0.25em] text-ink/60">{{ __('Things I\'ve built, live from GitHub.') }}</p>
            </div>

            @if ($total)
                <dl class="flex flex-wrap gap-3 font-mono">
                    <div class="rounded-md border border-ink/10 bg-ink/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-ink/50">{{ __('Projects') }}</dt>
                        <dd class="text-lg text-ink">{{ $total }}</dd>
                    </div>
                    <div class="rounded-md border border-ink/10 bg-ink/5 px-4 py-2">
                        <dt class="text-[10px] uppercase tracking-wider text-ink/50">{{ __('Languages') }}</dt>
                        <dd class="text-lg text-ink">{{ $languages->count() }}</dd>
                    </div>
                    @if ($stars)
                        <div class="rounded-md border border-ink/10 bg-ink/5 px-4 py-2">
                            <dt class="text-[10px] uppercase tracking-wider text-ink/50">{{ __('Stars') }}</dt>
                            <dd class="text-lg text-ink">{{ $stars }}</dd>
                        </div>
                    @endif
                </dl>
            @endif
        </div>

        @if (! $total)
            <div class="rounded-md border border-dashed border-ink/20 p-10 text-center font-mono">
                <p class="text-ink">{{ __('GitHub isn\'t connected yet.') }}</p>
                <p class="mt-2 text-xs text-ink/50">{{ __('Projects will appear here after the next sync.') }}</p>
            </div>
        @else
            <!-- Language filter: links work without JavaScript; Tabs.js switches panels in place without a reload. -->
            <div data-tabs="language"
                 data-tab-active="bg-ink text-paper"
                 data-tab-idle="text-ink/70 hover:bg-ink/5 hover:text-ink">
                @if ($languages->count() > 1)
                    <nav class="no-scrollbar mb-8 flex gap-1 overflow-x-auto" aria-label="{{ __('Filter by language') }}">
                        <a href="{{ route('projects') }}" data-tab="" @if (! $language) aria-current="page" @endif
                           class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ ! $language ? 'bg-ink text-paper' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}">
                            {{ __('All') }} <span class="opacity-60">{{ $total }}</span>
                        </a>
                        @foreach ($languages as $name => $count)
                            @php($active = $name === $language)
                            <a href="{{ route('projects', ['language' => $name]) }}" data-tab="{{ $name }}" @if ($active) aria-current="page" @endif
                               class="shrink-0 rounded-md px-3 py-2 font-mono text-xs uppercase tracking-wider transition {{ $active ? 'bg-ink text-paper' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}">
                                {{ $name }} <span class="opacity-60">{{ $count }}</span>
                            </a>
                        @endforeach
                    </nav>
                @endif

                <!-- All: featured first, then the rest -->
                <div data-tab-panel="" @if ($language) hidden @endif>
                    @if ($featured->isNotEmpty())
                        <section class="mb-10">
                            <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('Featured') }}</h2>
                            <div class="grid gap-4 md:grid-cols-2">
                                @foreach ($featured as $repo)
                                    <x-project-card :repo="$repo" :index="$loop->index" large />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($others->isNotEmpty())
                        <section>
                            @if ($featured->isNotEmpty())
                                <h2 class="mb-4 font-mono text-sm uppercase tracking-wider text-ink">{{ __('More projects') }}</h2>
                            @endif
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($others as $repo)
                                    <x-project-card :repo="$repo" :index="$loop->index" />
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <!-- One panel per language -->
                @foreach ($languages as $name => $count)
                    <div data-tab-panel="{{ $name }}" @if ($name !== $language) hidden @endif>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($repos->where('language', $name) as $repo)
                                <x-project-card :repo="$repo" :index="$loop->index" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($lastSynced)
                <p class="mt-8 text-right font-mono text-[10px] uppercase tracking-wider text-ink/40">
                    {{ __('Updated :time', ['time' => $lastSynced->diffForHumans()]) }}
                </p>
            @endif
        @endif
    </x-glass-panel>
</x-layouts.site>
