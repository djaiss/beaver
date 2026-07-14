@props([
  'title' => null,
  'padding' => 'p-4',
  'description' => null,
  'additionalInfo' => null,
])

<div class="flex flex-col gap-2">
  @isset($title)
    <div class="flex items-center justify-between">
      @isset($title)
        <h2 class="mb-1 text-lg font-semibold text-ink">{{ $title }}</h2>
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
