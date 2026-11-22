@php
    $links = [
        ['route' => 'profile.edit', 'label' => __('Profile')],
        ['route' => 'user-password.edit', 'label' => __('Password')],
    ];

    if (Laravel\Fortify\Features::canManageTwoFactorAuthentication()) {
        $links[] = ['route' => 'two-factor.show', 'label' => __('Two-Factor Auth')];
    }
@endphp

<div class="flex items-start gap-10 max-md:flex-col max-md:gap-6">
    <nav class="no-scrollbar flex w-full gap-1 md:w-[200px] md:flex-col max-md:overflow-x-auto">
        @foreach ($links as $link)
            @php($active = request()->routeIs($link['route']))
            <a
                href="{{ route($link['route']) }}"
                wire:navigate
                @if ($active) aria-current="page" @endif
                class="shrink-0 rounded-md px-3 py-2 font-mono text-sm uppercase tracking-wider transition {{ $active ? 'bg-azukigray-400 text-[#09090b]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}"
            >{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <div class="w-full flex-1 self-stretch">
        <h2 class="font-mono text-lg uppercase tracking-wider text-white">{{ $heading ?? '' }}</h2>
        <p class="mt-1 font-mono text-xs text-white/60">{{ $subheading ?? '' }}</p>

        <div class="mt-6 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
