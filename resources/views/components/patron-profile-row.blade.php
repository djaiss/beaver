<div class="grid grid-cols-[auto_1fr] gap-x-8 px-4 py-4 items-start border-b last:border-b-0 border-gray-200">
  <dt class="flex items-center gap-2 text-sm text-gray-500 min-w-40">
    {{ $icon ?? '' }}
    {{ $label }}
  </dt>
  <dd class="text-sm text-gray-900">{{ $slot }}</dd>
</div>
