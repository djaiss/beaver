<x-app-layout>
  <x-slot:title>
    Deletion reasons
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-4xl space-y-6">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">Deletion reasons</h1>
        <p class="mt-1 text-sm text-muted">{{ $reasons->total() }} of {{ $totalCount }} reasons</p>
      </div>

      <p class="text-sm text-muted">
        What people wrote when they deleted their user, newest first. A reason is kept on its own once the person is gone: it names nobody, and there is nothing to open or act on here. Reasons are encrypted at rest, so they cannot be searched.
      </p>

      <x-box padding="p-0">
        @forelse ($reasons as $reason)
          <div class="flex items-start justify-between gap-4 border-b border-hairline-soft px-4 py-3.5 last:border-b-0">
            @if (filled($reason->reason))
              <p class="min-w-0 text-sm whitespace-pre-line text-ink">{{ $reason->reason }}</p>
            @else
              <p class="min-w-0 text-sm text-muted-soft italic">No reason given.</p>
            @endif

            <span class="shrink-0 text-xs text-muted">{{ $reason->created_at->isoFormat('ll') }}</span>
          </div>
        @empty
          <x-empty-state>
            <x-slot:icon>
              @svg('lucide-door-open', 'size-5 text-muted')
            </x-slot>
            Nobody has deleted their user on this instance.
          </x-empty-state>
        @endforelse
      </x-box>

      {{ $reasons->links() }}
    </div>
  </div>
</x-app-layout>
