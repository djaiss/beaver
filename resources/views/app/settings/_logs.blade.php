<x-box padding="p-0" helpId="profile.logs">
  <x-slot:title>{{ __('Logs') }}</x-slot>
  <x-slot:description>
    <p>{{ __('All actions performed on your account are logged here.') }}</p>
  </x-slot>

  <!-- last actions -->
  @foreach ($logs as $log)
    <div class="flex items-center justify-between border-b border-hairline-soft p-3 text-sm last:border-b-0">
      <div class="flex items-center gap-3">
        <x-lucide-activity class="size-3 min-w-3 text-muted-soft" />
        <div class="flex flex-col gap-y-2">
          <p class="items-center gap-2 text-body sm:flex">
            <span class="font-semibold text-ink">{{ $log->username }}</span>
            |
            <span class="font-mono text-xs">{{ $log->action }}</span>
          </p>
          <p class="text-body">{{ $log->description }}</p>
        </div>
      </div>

      <x-tooltip text="{{ $log->created_at }}">
        <p class="font-mono text-xs text-muted-soft">{{ $log->created_at_human }}</p>
      </x-tooltip>
    </div>
  @endforeach

  @if ($hasMoreLogs)
    <div class="flex justify-center p-3 text-sm">
      <x-link href="{{ route('profile.logs.index') }}" class="text-center">{{ __('Browse all activity') }}</x-link>
    </div>
  @endif
</x-box>
