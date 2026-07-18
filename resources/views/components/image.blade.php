@props([
  'src',
  'alt',
  'width',
  'height',
  'srcset' => null,
])

{{--
  The image only starts downloading once it scrolls into the viewport: Alpine binds the src
  (and srcset) the first time the element intersects, then fades it in. Browsers without
  JavaScript get the plain image through the noscript fallback below.
--}}
<img
  x-data="{ visible: false }"
  x-intersect.once.margin.200px="visible = true"
  x-bind:src="visible ? {{ Js::from($src) }} : false"
  @if ($srcset) x-bind:srcset="visible ? {{ Js::from($srcset) }} : false" @endif
  x-bind:class="visible ? 'opacity-100' : 'opacity-0'"
  alt="{{ $alt }}"
  width="{{ $width }}"
  height="{{ $height }}"
  loading="lazy"
  decoding="async"
  {{ $attributes->class(['opacity-0 transition-opacity duration-300']) }}
/>

<noscript>
  <img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}" @if ($srcset) srcset="{{ $srcset }}" @endif loading="lazy" {{ $attributes }} />
</noscript>
