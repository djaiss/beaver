@props([
    'size' => 24,
    'hoverColor' => '#fb923c',
    'duration' => 200,
])

<span
    {{ $attributes->class(['logo inline-flex shrink-0']) }}
    style="--logo-hover-color: {{ $hoverColor }}; --logo-duration: {{ (int) $duration }}ms"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="{{ $size }}" height="{{ $size }}" role="img" aria-label="{{ config('app.name') }}">
        <rect x="16" y="16" width="992" height="992" rx="132" fill="#111111" />
        <g class="logo-mark" fill="#FFFFFF">
            <path d="M346,294.1 L346,706 L429,706 L429,588.8 L484,527.6 L601.2,706 L699.8,706 L542.1,472.6 L695.7,294.1 L594,294.1 L480.9,426.9 L429,499.5 L429,294.1 Z" />
            <path d="M493.3,760 H454.9 A13.5,13.5 0 0 0 441.4,773.5 V816 A13.5,13.5 0 0 0 454.9,829.5 H493.3 Z" />
            <path d="M502.7,760 H568 A13.5,13.5 0 0 1 581.5,773.5 V816 A13.5,13.5 0 0 1 568,829.5 H502.7 Z" />
        </g>
    </svg>
</span>
