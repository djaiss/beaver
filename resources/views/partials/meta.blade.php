<title>{{ $title ?? config('app.name') }}</title>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<meta name="description" content="{{ config('app.description') }}" />
<link rel="icon" type="image/png" href="{{ asset('images/marketing/logo/30x30@2x.png') }}" sizes="60x60" />

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
