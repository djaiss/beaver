@props([
  'src',
  'alt',
  'width',
  'height',
  'srcset' => null,
  'loading' => 'lazy',
])

{{-- The browser defers the download until the image nears the viewport on its own. --}}
<img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}" @if ($srcset) srcset="{{ $srcset }}" @endif loading="{{ $loading }}" decoding="async" {{ $attributes }} />
