<x-app-layout>
  <x-slot:title>
    {{ __('Logs') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Logs') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('All actions performed on your account are logged here.') }}</p>
      </div>

      <x-box id="logs-container" x-merge="append" padding="p-0">
        <!-- last actions -->
        @foreach ($logs as $log)
          <div class="flex items-center justify-between border-b border-hairline-soft p-3 text-sm last:border-b-0">
            <div class="flex items-center gap-3">
              <x-phosphor-pulse class="size-3 min-w-3 text-muted-soft" />
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

        @if ($logs->nextPageUrl())
          <div id="pagination" class="flex justify-center p-3 text-sm">
            <x-link x-target="logs-container pagination" href="{{ $logs->nextPageUrl() }}" class="text-center">{{ __('Load more') }}</x-link>
          </div>
        @endif
      </x-box>
    </div>
  </div>
</x-app-layout>
