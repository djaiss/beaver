<x-app-layout>
  <x-slot:title>
    {{ $ticket->subject }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-2xl space-y-8">
      <div>
        <a href="{{ route('support.tickets.index') }}" data-turbo="true" class="mb-5 inline-flex items-center gap-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
          @svg('lucide-arrow-left', 'size-4')
          {{ __('Back to conversations') }}
        </a>

        <div class="flex flex-wrap items-start justify-between gap-3">
          <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ $ticket->subject }}</h1>
          <x-badge :color="$ticket->status->badgeColor()">{{ $ticket->status->label() }}</x-badge>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted-soft">
          <span>{{ $ticket->category->label() }}</span>
          <span>&middot;</span>
          <span>{{ __('Opened :time', ['time' => $ticket->created_at->diffForHumans()]) }}</span>
        </div>
      </div>

      {{-- Messages --}}
      <div class="space-y-6">
        @foreach ($ticket->messages->sortBy('created_at') as $message)
          <div class="flex gap-4">
            <x-avatar :user="$message->user" :size="32" class="size-10 shrink-0 text-sm" />

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-ink">{{ $message->user->getFullName() }}</span>
                <span class="text-xs text-muted-soft">{{ $message->created_at->diffForHumans() }}</span>
              </div>
              <div class="mt-2 rounded-xl border border-hairline bg-canvas p-4 text-sm leading-relaxed whitespace-pre-line text-body">{{ $message->body }}</div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Reply or closed notice --}}
      @if ($ticket->status === \App\Enums\SupportTicketStatus::Open)
        <x-form method="post" action="{{ route('support.tickets.messages.create', $ticket) }}" class="ml-14 space-y-3 rounded-xl border border-hairline p-4">
          <textarea
            id="body"
            name="body"
            rows="4"
            placeholder="{{ __('Write a reply…') }}"
            class="block w-full resize-y rounded-md border border-hairline bg-input px-3 py-2.5 text-sm leading-relaxed text-ink placeholder-muted-soft shadow-xs aria-invalid:border-error dark:shadow-none"
          >{{ old('body') }}</textarea>
          <x-error :messages="$errors->get('body')" />

          <div class="flex justify-end">
            <x-button type="submit" data-test="reply-button">
              <x-slot:icon>
                <x-lucide-send class="size-4" />
              </x-slot>
              {{ __('Reply') }}
            </x-button>
          </div>
        </x-form>
      @else
        <div class="ml-14 flex items-start gap-3 rounded-xl border border-hairline bg-card p-4 text-sm text-muted">
          <x-lucide-check class="mt-0.5 size-4 shrink-0" />
          <div class="space-y-0.5">
            @if ($ticket->closed_by)
              <p>
                {{ __('Closed by') }}
                <span class="font-semibold text-ink">{{ $ticket->closed_by->label() }}</span>
                @if ($ticket->closed_at)
                  <span class="text-muted-soft">&middot; {{ $ticket->closed_at->diffForHumans() }}</span>
                @endif
              </p>
            @else
              <p>{{ __('This conversation is closed.') }}</p>
            @endif
            <p>{{ __('Need more help?') }} <a href="{{ route('support.tickets.new') }}" data-turbo="true" class="font-semibold text-ink hover:underline">{{ __('Start a new one') }}</a></p>
          </div>
        </div>
      @endif

      {{-- Conversation actions --}}
      <div class="flex flex-wrap items-center gap-3 border-t border-hairline pt-6">
        @if ($ticket->status === \App\Enums\SupportTicketStatus::Open)
          <x-form method="put" action="{{ route('support.tickets.update', $ticket) }}">
            <x-button.secondary type="submit" data-test="close-conversation-button">{{ __('Close conversation') }}</x-button.secondary>
          </x-form>
        @endif

        <x-form
          method="delete"
          action="{{ route('support.tickets.destroy', $ticket) }}"
          onsubmit="return confirm('{{ __('Are you sure you want to delete this conversation? This can not be undone.') }}')"
        >
          <x-button.secondary type="submit" data-test="delete-conversation-button">{{ __('Delete conversation') }}</x-button.secondary>
        </x-form>
      </div>
    </div>
  </div>
</x-app-layout>
