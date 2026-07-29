{{--
  The social and crawler half of the head, for the public pages only. Everything here comes
  from App\ViewModels\MarketingSeo, which decides which URL owns the page and which languages
  are allowed to claim it. The application and the error pages sit behind auth or behind a
  4xx, so they get none of this.

  Include it right after partials.meta, and pass the same $seo array the page fed to it,
  along with the $structuredData graph from App\ViewModels\MarketingStructuredData.
--}}

<link rel="canonical" href="{{ $seo['canonical'] }}" />

@foreach ($seo['alternates'] as $alternate)
  <link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['url'] }}" />
@endforeach

<meta property="og:type" content="{{ $seo['type'] }}" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta property="og:url" content="{{ $seo['canonical'] }}" />
<meta property="og:title" content="{{ $seo['ogTitle'] }}" />
<meta property="og:description" content="{{ $seo['description'] }}" />
<meta property="og:image" content="{{ $seo['image'] }}" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="{{ config('app.name') }}" />
<meta property="og:locale" content="{{ $seo['locale'] }}" />

@foreach ($seo['alternateLocales'] as $alternateLocale)
  <meta property="og:locale:alternate" content="{{ $alternateLocale }}" />
@endforeach

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seo['ogTitle'] }}" />
<meta name="twitter:description" content="{{ $seo['description'] }}" />
<meta name="twitter:image" content="{{ $seo['image'] }}" />

{{-- One graph rather than a script tag per entity, so the organisation is declared once and
     the rest of the page points at it. JSON_HEX_TAG is what keeps a translated answer that
     happens to contain a closing tag from ending the script early. --}}
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
