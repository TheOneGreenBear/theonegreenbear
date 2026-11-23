<nav class="site-nav relative bg-paper/90 backdrop-blur-sm after:pointer-events-none after:absolute after:inset-x-0 after:bottom-0 after:h-px after:bg-ink/10">
  <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
    <div class="relative flex h-16 items-center justify-between">
      <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
        <!-- Mobile menu button-->
        <button type="button" aria-controls="mobile-menu" aria-expanded="false"
          onclick="const open = this.getAttribute('aria-expanded') !== 'true'; this.setAttribute('aria-expanded', open); document.getElementById('mobile-menu').hidden = !open;"
          class="relative inline-flex items-center justify-center rounded-md p-2 text-ink/60 hover:bg-ink/5 hover:text-ink focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
          <span class="absolute -inset-0.5"></span>
          <span class="sr-only">Open main menu</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center transition hover:scale-105">
          <img src="{{ asset('img/TheOneGreenBearLogo.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
        </a>
        @php
          // Small, widely spaced uppercase links; the current page gets a red underline.
          $navLink = fn (bool $active) => 'px-3 py-2 font-mono text-xs uppercase tracking-[0.2em] transition '
              .($active ? 'text-ink underline decoration-azuki decoration-2 underline-offset-8' : 'text-ink/60 hover:text-ink');
        @endphp
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <div class="flex space-x-2">
            <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif class="{{ $navLink(request()->routeIs('home')) }}">Home</a>
            <a href="#" class="{{ $navLink(false) }}">About me</a>
            <a href="{{ route('projects') }}" @if (request()->routeIs('projects')) aria-current="page" @endif class="{{ $navLink(request()->routeIs('projects')) }}">Projects</a>
            <a href="#" class="{{ $navLink(false) }}">Contact</a>
          </div>
        </div>
      </div>
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
        <!-- Profile dropdown -->
          @auth
            <a href="{{ route('profile.edit') }}" class="relative flex size-8 items-center justify-center rounded-full bg-sand font-mono text-xs text-ink outline -outline-offset-1 outline-ink/10 hover:bg-sand-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
              <span class="sr-only">Your settings</span>
              <span aria-hidden="true">{{ auth()->user()->initials() }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-5 py-1.5 font-mono text-xs uppercase tracking-[0.2em] text-ink/60 hover:text-ink transition">Log out</button>
            </form>
          @else
              <a href="{{ route('login') }}" class="hidden sm:inline-block px-5 py-1.5 font-mono text-xs uppercase tracking-[0.2em] text-ink/60 hover:text-ink transition">Log in</a>
              @if (Route::has('register'))
                  <a href="{{ route('register') }}" class="hidden sm:inline-block px-5 py-2 font-mono text-xs uppercase tracking-[0.2em] text-white bg-azuki rounded-sm hover:bg-azuki-dark transition">Register</a>
              @endif
          @endauth
      </div>
    </div>
  </div>

  <div id="mobile-menu" hidden class="block sm:hidden">
    <div class="space-y-1 px-2 pt-2 pb-3">
      <!-- Current: "bg-ink/5 text-ink", Default: "text-ink/70 hover:bg-ink/5 hover:text-ink" -->
      <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif class="block rounded-md px-3 py-2 text-base font-mono {{ request()->routeIs('home') ? 'bg-ink/5 text-ink' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}">Home</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-mono text-ink/70 hover:bg-ink/5 hover:text-ink">About me</a>
      <a href="{{ route('projects') }}" @if (request()->routeIs('projects')) aria-current="page" @endif class="block rounded-md px-3 py-2 text-base font-mono {{ request()->routeIs('projects') ? 'bg-ink/5 text-ink' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}">Projects</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-mono text-ink/70 hover:bg-ink/5 hover:text-ink">Contact</a>
      @guest
        <div class="mt-2 border-t border-ink/10 pt-2">
          <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-mono text-ink/70 hover:bg-ink/5 hover:text-ink">Log in</a>
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-mono text-ink/70 hover:bg-ink/5 hover:text-ink">Register</a>
          @endif
        </div>
      @endguest
    </div>
  </div>
</nav>
