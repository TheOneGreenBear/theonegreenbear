{{-- Decorative HUD line art in the style of a game-intel file: leader lines with 45° bends, square
     nodes, dotted runs, corner brackets and tiny labels. Drawn in currentColor. On load the solid
     lines draw themselves in (pathLength="1" lets the CSS animate them as 0-1), then the nodes,
     dotted runs and labels fade in; a few nodes keep blinking. --d is each piece's start delay. --}}
<svg {{ $attributes }} viewBox="0 0 1200 700" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true">
    <g stroke="currentColor" stroke-width="1" opacity="0.55">
        {{-- Corner brackets around the whole frame. --}}
        <path class="hud-draw" style="--d: 0s" pathLength="1" d="M48 64 V48 H64" />
        <path class="hud-draw" style="--d: 0.05s" pathLength="1" d="M1136 48 H1152 V64" />
        <path class="hud-draw" style="--d: 0.1s" pathLength="1" d="M48 636 V652 H64" />
        <path class="hud-draw" style="--d: 0.15s" pathLength="1" d="M1136 652 H1152 V636" />

        {{-- Top run: from the title out towards the bear. --}}
        <path class="hud-draw" style="--d: 0.2s" pathLength="1" d="M96 150 H430 L480 100 H770" />
        <rect class="hud-fade" style="--d: 0.7s" x="424" y="144" width="12" height="12" />
        <path class="hud-fade" style="--d: 1.1s" d="M770 100 L810 140 H1090" stroke-dasharray="2 7" />
        <rect class="hud-fade" style="--d: 1.3s" x="1084" y="134" width="12" height="12" />

        {{-- Bottom run: under the intro text, kinking down and away. --}}
        <path class="hud-draw" style="--d: 0.5s" pathLength="1" d="M96 590 H380 L420 550" />
        <path class="hud-fade" style="--d: 1.2s" d="M420 550 L470 600 H560" stroke-dasharray="8 7" />
        <rect class="hud-fade" style="--d: 0.9s" x="374" y="584" width="12" height="12" />

        {{-- Right side: a short drop beside the bear. --}}
        <path class="hud-draw" style="--d: 0.8s" pathLength="1" d="M1110 260 V430 L1070 470 H980" />
    </g>

    {{-- Small square nodes and dotted marks. --}}
    <g fill="currentColor" opacity="0.7">
        <rect class="hud-fade" style="--d: 0.9s" x="455" y="88" width="5" height="5" />
        <rect class="hud-blink" style="--d: 1.4s" x="475" y="88" width="5" height="5" />
        <rect class="hud-fade" style="--d: 1s" x="465" y="68" width="5" height="5" />
        <rect class="hud-fade" style="--d: 1.3s" x="510" y="596" width="5" height="5" />
        <rect class="hud-blink" style="--d: 1.8s" x="530" y="596" width="5" height="5" />
        <rect class="hud-fade" style="--d: 1.5s" x="550" y="596" width="5" height="5" />
        <rect class="hud-fade" style="--d: 1.2s" x="1105" y="240" width="5" height="5" />
        <rect class="hud-blink" style="--d: 2.2s" x="1125" y="240" width="5" height="5" />
    </g>

    {{-- Tiny labels: they decode themselves once the lines are in. --}}
    <g class="hud-fade" style="--d: 1.2s" fill="currentColor" font-family="ui-monospace, SFMono-Regular, Menlo, monospace" font-size="9" letter-spacing="2" opacity="0.6">
        <text data-scramble data-scramble-delay="1200" x="820" y="128">INDEX // 01-06</text>
        <text data-scramble data-scramble-delay="1400" x="96" y="616">GAMES · ANIME · MUSIC · CODE</text>
        <text data-scramble data-scramble-delay="1600" x="980" y="490">LIVE FEED</text>
    </g>
</svg>
