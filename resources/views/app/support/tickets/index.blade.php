<x-app-layout>
  <x-slot:title>
    {{ __('Support') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Your conversations') }}</h1>
          <p class="mt-1 text-sm text-muted">{{ __('Messages you\'ve sent to support, and every reply you get back.') }}</p>
        </div>

        <x-button href="{{ route('support.tickets.new') }}" turbo="true" data-test="new-conversation-button">
          <x-slot:icon>
            <x-lucide-plus class="size-4" />
          </x-slot>
          {{ __('New conversation') }}
        </x-button>
      </div>

      @forelse ($tickets as $ticket)
        @php
          $lastMessage = $ticket->messages->last();
        @endphp
        <a
          href="{{ route('support.tickets.show', $ticket) }}"
          data-turbo="true"
          data-test="conversation-{{ $ticket->id }}"
          class="flex items-start gap-4 rounded-xl border border-hairline bg-canvas p-5 transition-colors hover:bg-card"
        >
          <span @class([
            'mt-1.5 size-2.5 shrink-0 rounded-full',
            'bg-success' => $ticket->status === \App\Enums\SupportTicketStatus::Open,
            'bg-badge-violet' => $ticket->status === \App\Enums\SupportTicketStatus::Answered,
            'bg-muted-soft' => $ticket->status === \App\Enums\SupportTicketStatus::Closed,
          ])></span>

          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-[15px] font-semibold text-ink">{{ $ticket->subject }}</span>
              <x-badge :color="$ticket->status->badgeColor()" class="!px-2 !py-0.5 !text-xs">{{ $ticket->status->label() }}</x-badge>
            </div>

            @if ($lastMessage)
              <p class="mt-1 truncate text-sm text-muted">{{ $lastMessage->body }}</p>
            @endif

            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted-soft">
              <span>{{ $ticket->category->label() }}</span>
              <span>&middot;</span>
              <span>{{ trans_choice('{1} :count message|[2,*] :count messages', $ticket->messages_count, ['count' => $ticket->messages_count]) }}</span>
              <span>&middot;</span>
              <span>{{ __('Updated :time', ['time' => $ticket->updated_at->diffForHumans()]) }}</span>
            </div>
          </div>

          <x-lucide-chevron-right class="mt-1 size-4 shrink-0 text-muted-soft" />
        </a>
      @empty
        <x-empty-state class="rounded-xl border border-dashed border-hairline">
          <x-slot:icon>
            <x-lucide-messages-square class="size-6 text-muted" />
          </x-slot>

          <span class="text-base font-semibold text-ink">{{ __('No conversations yet') }}</span>
          {{ __('When you message support, your conversations show up here so you can follow every reply in one place.') }}

          <div class="mt-4">
            <x-button href="{{ route('support.tickets.new') }}" turbo="true">
              <x-slot:icon>
                <x-lucide-plus class="size-4" />
              </x-slot>
              {{ __('Start a conversation') }}
            </x-button>
          </div>
        </x-empty-state>
      @endforelse
    </div>
  </div>
</x-app-layout>
