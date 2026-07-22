@props([
  'title' => null,
  'helpId' => null,
  'padding' => 'p-4',
  'description' => null,
  'additionalInfo' => null,
])

<div class="flex flex-col gap-2">
  @isset($title)
    <div class="flex items-center justify-between">
      @isset($title)
        <div class="mb-1 flex items-center gap-2">
          <h2 class="text-lg font-semibold text-ink">{{ $title }}</h2>
          @if ($helpId)
            <x-help :id="$helpId" />
          @endif
        </div>
      @endisset

      @isset($actions)
        <div>{{ $actions }}</div>
      @endisset
    </div>
  @endisset

  @isset($description)
    <div class="mb-2 flex flex-col gap-y-2 text-sm text-muted">
      {{ $description }}
    </div>
  @endisset

  @isset($additionalInfo)
    {{ $additionalInfo }}
  @endisset

  <div {{ $attributes->merge(['class' => 'rounded-lg border border-hairline bg-canvas ' . $padding]) }}>
    {{ $slot }}
  </div>
</div>
