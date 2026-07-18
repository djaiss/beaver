{{-- The pair of hero style CTAs. Signed in visitors get sent to their account instead. --}}
<div {{ $attributes->class(['flex flex-col justify-center gap-3 sm:flex-row']) }}>
  @auth
    <a href="{{ route('dashboard.index') }}" class="flex h-12 items-center justify-center rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">Go to your account</a>
  @else
    <a href="{{ route('register') }}" class="flex h-12 items-center justify-center rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">Get started</a>
  @endauth

  <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="flex h-12 items-center justify-center gap-x-2 rounded-md border border-hairline bg-canvas px-5.5 text-[15px] font-semibold text-ink transition-colors hover:bg-sidebar">
    <x-lucide-github class="h-[18px] w-[18px]" />
    View on GitHub
  </a>
</div>
