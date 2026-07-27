<title>{{ $title ?? config('app.name') }}</title>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

{{-- The public pages hand in their own description through App\Services\MarketingSeo;
     everything else falls back to the one line summary of the application. --}}
@php($metaDescription = $description ?? config('app.description'))

@if ($metaDescription)
  <meta name="description" content="{{ $metaDescription }}" />
@endif

<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
<link rel="alternate icon" href="{{ asset('favicon.ico') }}" sizes="32x32" />

<link rel="preconnect" href="https://fonts.bunny.net" />
<link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=jetbrains-mono:400,500&display=swap" rel="stylesheet" />

{{-- Apply the saved theme before paint to avoid a flash of the wrong theme. --}}
<script>
  (function () {
    try {
      var t = localStorage.getItem('theme');
      if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
      }
    } catch (e) {}
  })();
</script>
