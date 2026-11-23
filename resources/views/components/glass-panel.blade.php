{{-- Content area on the white page. Flat by default; pass border/shadow classes for a card (e.g. the auth forms). --}}
<div {{ $attributes->merge(['class' => 'min-w-0 bg-paper']) }}>
    {{ $slot }}
</div>
