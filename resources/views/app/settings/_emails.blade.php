<x-box padding="p-0">
  <x-slot:title>{{ __('Emails sent') }}</x-slot>

  @forelse ($emails as $emailSent)
    <div x-data="{ open: false, isLast: {{ $loop->last ? 'true' : 'false' }} }">
      <div @click="open = !open" class="group flex cursor-pointer items-center justify-between border-b border-hairline-soft p-3 text-sm text-body last:border-b-0" :class="{'border-b-0': !open && isLast}">
        <div class="flex items-center gap-x-3">
          @if ($emailSent->sent_at && ! $emailSent->delivered_at)
            <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-warning"></span>
          @elseif ($emailSent->delivered_at && $emailSent->sent_at)
            <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-success"></span>
          @elseif ($emailSent->bounced_at)
            <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-error"></span>
          @endif

          <div class="flex flex-col gap-1">
            <div>
              <span class="font-light text-muted">{{ __('To:') }}</span>
              {{ $emailSent->email_address }}
            </div>
            <div>
              <span class="font-light text-muted">{{ __('Subject:') }}</span>
              {{ $emailSent->subject }}
            </div>
          </div>
        </div>

        <div class="flex items-center gap-x-3">
          <!-- sent at && delivered at -->
          <div class="flex flex-col gap-1">
            <div>
              <span class="font-light text-muted">{{ __('Sent at:') }}</span>
              {{ $emailSent->sent_at?->diffForHumans() }}
            </div>

            @if ($emailSent->delivered_at)
              <div>
                <span class="font-light text-muted">{{ __('Delivered at:') }}</span>
                {{ $emailSent->delivered_at?->diffForHumans() }}
              </div>
            @endif
          </div>

          <!-- arrow -->
          <x-phosphor-caret-down x-show="!open" class="h-4 w-4 text-muted transition-transform duration-200" />
          <x-phosphor-caret-up x-show="open" class="h-4 w-4 text-muted transition-transform duration-200" />
        </div>
      </div>

      <div x-cloak x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 transform opacity-0" x-transition:enter-end="translate-y-0 transform opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0 transform opacity-100" x-transition:leave-end="-translate-y-2 transform opacity-0" class="border-b border-hairline-soft bg-card" :class="{'border-b-0': isLast}">
        <p class="p-2 text-center text-muted italic">{{ __('We automatically remove links in this email since they are probably invalid at this time') }}</p>
        <div class="p-4">
          {!! $emailSent->body !!}
        </div>
      </div>
    </div>
  @empty
    <x-empty-state>
      <x-slot:icon>
        <x-phosphor-building-office class="size-6 text-muted" />
      </x-slot>
      {{ __('No emails have been sent yet.') }}
    </x-empty-state>
  @endforelse
</x-box>
