@props(['testimonial'])

@php
  $safeLink = $testimonial->safeLink();
@endphp

<div class="rounded-lg border border-hairline bg-canvas p-5">
  <div class="mb-3 flex items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-3">
      <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-badge-violet text-sm font-semibold text-white">{{ $testimonial->initial() }}</span>
      <div class="min-w-0">
        @if ($safeLink)
          <a href="{{ $safeLink }}" target="_blank" rel="nofollow ugc noopener" class="text-sm font-semibold text-ink underline decoration-hairline underline-offset-2 hover:decoration-ink">{{ $testimonial->name }}</a>
        @else
          <p class="truncate text-sm font-semibold text-ink">{{ $testimonial->name }}</p>
        @endif
      </div>
    </div>
    <x-badge :color="$testimonial->status->color()">{{ $testimonial->status->label() }}</x-badge>
  </div>
  <p class="text-[15px] leading-relaxed text-muted">{{ $testimonial->body }}</p>
</div>
