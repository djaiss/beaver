<x-app-layout>
  <x-slot:title>
    Support tickets
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-6xl space-y-6">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">Support tickets</h1>
        <p class="mt-1 text-sm text-muted">Messages sent to support from across the instance.</p>
      </div>

      {{-- Tabs. Each bucket is a dedicated URL, so switching one is a normal
           navigation and can be linked to or bookmarked. --}}
      @php
        $tabs = [
          ['key' => 'open', 'label' => 'Open', 'count' => $openCount],
          ['key' => 'all', 'label' => 'All', 'count' => $allCount],
          ['key' => 'closed', 'label' => 'Closed', 'count' => $closedCount],
        ];
      @endphp
      <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $tab)
          @php($active = $status === $tab['key'])
          <a
            href="{{ route('instanceAdmin.support.index', ['status' => $tab['key']]) }}"
            data-turbo="true"
            @class([
              'inline-flex items-center gap-2 rounded-full border px-3.5 py-1.5 text-[13px] font-medium transition-colors',
              'border-hairline bg-card text-ink' => $active,
              'border-transparent text-muted hover:text-ink' => ! $active,
            ])
          >
            {{ $tab['label'] }}
            <span
              @class([
                'inline-flex min-w-5 items-center justify-center rounded-full px-1.5 py-0.5 text-[11px] font-semibold',
                'bg-ink text-canvas' => $active,
                'bg-card text-muted-soft' => ! $active,
              ])
            >{{ $tab['count'] }}</span>
          </a>
        @endforeach
      </div>

      @if ($tickets->isEmpty())
        {{-- A blank state phrased for the bucket the admin is looking at. --}}
        <x-box padding="p-0">
          <x-empty-state>
            <x-slot:icon>
              @svg('lucide-message-square', 'size-5 text-muted')
            </x-slot>
            @switch($status)
              @case('closed')
                No closed conversations yet.
                @break
              @case('all')
                No support conversations yet. When someone writes to support, their message lands here.
                @break
              @default
                No open conversations. When someone writes to support, their message lands here.
            @endswitch
          </x-empty-state>
        </x-box>
      @else
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
          {{-- Ticket list --}}
          <div class="flex flex-col gap-2.5">
            @foreach ($tickets as $ticket)
              @php($isSelected = $selected?->id === $ticket->id)
              <a
                href="{{ route('instanceAdmin.support.index', ['status' => $status, 'ticket' => $ticket->id]) }}"
                data-turbo="true"
                @class([
                  'block rounded-xl border bg-canvas p-4 transition-colors',
                  'border-ink' => $isSelected,
                  'border-hairline hover:border-muted-soft' => ! $isSelected,
                ])
              >
                <div class="flex items-center justify-between gap-2">
                  <span class="text-xs text-muted-soft">{{ $ticket->category->label() }}</span>
                  <x-badge :color="$ticket->status->badgeColor()">{{ $ticket->status->label() }}</x-badge>
                </div>

                <p class="mt-2 truncate text-sm font-semibold text-ink">{{ $ticket->subject }}</p>

                <div class="mt-3 flex items-center gap-2">
                  <x-avatar :user="$ticket->user" :size="32" class="size-6 shrink-0 text-[10px]" />
                  <span class="min-w-0 flex-1 truncate text-xs text-muted">{{ $ticket->user->getFullName() }}</span>
                  <span class="shrink-0 text-xs text-muted-soft">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
              </a>
            @endforeach
          </div>

          {{-- Conversation detail --}}
          <div class="flex min-h-0 flex-col rounded-xl border border-hairline bg-canvas">
            @if ($selected)
              <div class="border-b border-hairline p-5">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <h2 class="text-base font-semibold text-ink">{{ $selected->subject }}</h2>
                    <p class="mt-1 text-xs text-muted-soft">
                      Ticket #{{ $selected->id }}
                      &middot;
                      opened {{ $selected->created_at->diffForHumans() }}
                      &middot;
                      {{ $selected->category->label() }}
                    </p>
                  </div>
                  <x-badge :color="$selected->status->badgeColor()">{{ $selected->status->label() }}</x-badge>
                </div>

                <div class="mt-4 flex items-center gap-3">
                  <x-avatar :user="$selected->user" :size="32" class="size-8 shrink-0 text-xs" />
                  <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-ink">{{ $selected->user->getFullName() }}</p>
                    <p class="truncate text-xs text-muted-soft">{{ $selected->user->email }}</p>
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-5 p-5">
                @foreach ($selected->messages->sortBy('created_at') as $message)
                  {{-- Team replies sit on the right with an accent bubble, the way an
                       outgoing message reads; the user's messages stay on the left. --}}
                  <div @class(['flex gap-3', 'flex-row-reverse' => $message->is_from_team])>
                    <x-avatar :user="$message->user" :size="32" class="size-7 shrink-0 text-[10px]" />
                    <div class="min-w-0 flex-1">
                      <div @class(['flex flex-wrap items-center gap-2', 'justify-end' => $message->is_from_team])>
                        <span class="text-[13px] font-semibold text-ink">{{ $message->user->getFullName() }}</span>
                        @if ($message->is_from_team)
                          <x-badge color="violet" class="!px-2 !py-0.5 !text-xs">Team</x-badge>
                        @endif
                        <span class="text-xs text-muted-soft">{{ $message->created_at->diffForHumans() }}</span>
                      </div>
                      <div @class([
                        'mt-1.5 rounded-xl border p-3 text-[13px] leading-relaxed whitespace-pre-line',
                        'border-hairline bg-card text-body' => ! $message->is_from_team,
                        'border-transparent bg-accent/10 text-ink' => $message->is_from_team,
                      ])>{{ $message->body }}</div>
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- Reply as the team. Sending marks the conversation answered and
                   emails the person who opened it. --}}
              <div class="mt-auto space-y-3 border-t border-hairline p-5">
                <x-form method="post" :action="route('instanceAdmin.support.messages.create', $selected->id)" class="space-y-2">
                  <textarea
                    name="body"
                    rows="3"
                    placeholder="Write a reply…"
                    class="block w-full resize-y rounded-lg border border-hairline bg-input px-3 py-2 text-[13px] leading-relaxed text-ink placeholder-muted-soft shadow-xs aria-invalid:border-error dark:shadow-none"
                  >{{ old('body') }}</textarea>
                  <x-error :messages="$errors->get('body')" />

                  <x-button type="submit" data-test="team-reply-button">
                    <x-slot:icon>
                      <x-lucide-send class="size-4" />
                    </x-slot>
                    Send reply
                  </x-button>
                </x-form>

                <div class="flex items-center gap-3 text-xs text-muted">
                  @if ($selected->status === \App\Enums\SupportTicketStatus::Closed)
                    <span class="inline-flex items-center gap-2">
                      @svg('lucide-check', 'size-4 shrink-0')
                      Closed {{ $selected->closed_at?->diffForHumans() }}
                    </span>
                    <div class="flex-1"></div>
                    <x-form method="put" :action="route('instanceAdmin.support.update', $selected->id)">
                      <input type="hidden" name="status" value="open" />
                      <x-button.secondary type="submit" data-test="reopen-button">Reopen conversation</x-button.secondary>
                    </x-form>
                  @else
                    <div class="flex-1"></div>
                    <x-form method="put" :action="route('instanceAdmin.support.update', $selected->id)">
                      <input type="hidden" name="status" value="closed" />
                      <x-button.secondary type="submit" data-test="close-button">Close conversation</x-button.secondary>
                    </x-form>
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>
</x-app-layout>
