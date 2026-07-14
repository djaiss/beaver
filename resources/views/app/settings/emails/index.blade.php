<x-app-layout>
  <x-slot:title>
    {{ __('Emails sent') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Emails sent') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Every email we have sent to your account.') }}</p>
      </div>

      <x-box id="emails-sent-container" x-merge="append" padding="p-0">
        <!-- last actions -->
        @foreach ($emails as $email)
          <div x-data="{ open: false, isLast: {{ $loop->last ? 'true' : 'false' }} }">
            <div @click="open = !open" class="group flex cursor-pointer items-center justify-between border-b border-hairline-soft p-3 text-sm text-body last:border-b-0" :class="{'border-b-0': !open && isLast}">
              <div class="flex items-center gap-x-3">
                @if ($email->sent_at && ! $email->delivered_at)
                  <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-warning"></span>
                @elseif ($email->delivered_at && $email->sent_at)
                  <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-success"></span>
                @elseif ($email->bounced_at)
                  <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-canvas bg-error"></span>
                @endif

                <div class="flex flex-col gap-1">
                  <div>
                    <span class="font-light text-muted">{{ __('To:') }}</span>
                    {{ $email->email_address }}
                  </div>
                  <div>
                    <span class="font-light text-muted">{{ __('Subject:') }}</span>
                    {{ $email->subject }}
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-x-3">
                <!-- sent at && delivered at -->
                <div class="flex flex-col gap-1">
                  <div>
                    <span class="font-light text-muted">{{ __('Sent at:') }}</span>
                    {{ $email->sent_at }}
                  </div>

                  @if ($email->delivered_at)
                    <div>
                      <span class="font-light text-muted">{{ __('Delivered at:') }}</span>
                      {{ $email->delivered_at }}
                    </div>
                  @endif
                </div>

                <!-- arrow -->
                <x-lucide-chevron-down x-show="!open" class="h-4 w-4 text-muted transition-transform duration-200" />
                <x-lucide-chevron-up x-show="open" class="h-4 w-4 text-muted transition-transform duration-200" />
              </div>
            </div>

            <div x-cloak x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 transform opacity-0" x-transition:enter-end="translate-y-0 transform opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0 transform opacity-100" x-transition:leave-end="-translate-y-2 transform opacity-0" class="border-b border-hairline-soft bg-card" :class="{'border-b-0': isLast}">
              <p class="p-2 text-center text-muted italic">{{ __('We automatically remove links in this email since they are probably invalid at this time') }}</p>
              <div class="p-4">
                {!! $email->body !!}
              </div>
            </div>
          </div>
        @endforeach

        @if ($emails->nextPageUrl())
          <div id="pagination" class="flex justify-center p-3 text-sm">
            <x-link x-target="emails-sent-container pagination" href="{{ $emails->nextPageUrl() }}" class="text-center">{{ __('Load more') }}</x-link>
          </div>
        @endif
      </x-box>
    </div>
  </div>
</x-app-layout>
