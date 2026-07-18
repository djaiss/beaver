{{-- The product itself shown as markup rather than a screenshot, so it stays crisp and themable. --}}
<section class="mx-auto mt-10 max-w-[1200px] px-5 sm:mt-14 sm:px-8">
  <div class="overflow-hidden rounded-xl border border-hairline bg-canvas shadow-[0_24px_60px_rgba(17,17,17,0.10),0_4px_12px_rgba(17,17,17,0.05)]">
    {{-- Browser chrome --}}
    <div class="flex h-11 items-center gap-x-2 border-b border-hairline-soft bg-sidebar px-4">
      <span class="h-[11px] w-[11px] rounded-full bg-hairline"></span>
      <span class="h-[11px] w-[11px] rounded-full bg-hairline"></span>
      <span class="h-[11px] w-[11px] rounded-full bg-hairline"></span>
      <div class="ml-3 hidden h-[26px] max-w-[340px] flex-1 items-center rounded-sm border border-hairline bg-input px-2.5 text-xs text-muted-soft sm:flex">
        {{ Str::lower(config('app.name')) }}.app/dashboard
      </div>
    </div>

    <div class="flex min-h-[440px]">
      {{-- Sidebar. Hidden on small screens, where the real app collapses it too. --}}
      <div class="hidden w-[212px] shrink-0 flex-col gap-y-5 border-r border-hairline-soft bg-sidebar p-4 md:flex">
        <div class="flex items-center gap-x-2 px-1.5">
          <x-logo size="22" aria-hidden="true" />
          <x-wordmark height="14" class="text-ink" />
        </div>

        <div class="flex flex-col gap-y-0.5">
          <p class="px-2 py-1 text-[11px] font-semibold tracking-wide text-muted-soft uppercase">Workspace</p>
          @foreach ($dashboard['navigation'] as $item)
            <div class="flex items-center gap-x-2.5 rounded-md p-2 text-[13px] font-medium {{ $item['active'] ? 'bg-canvas text-ink' : 'text-body' }}">
              <span class="h-3.5 w-3.5 rounded-sm" style="background-color: {{ $item['dot'] }}"></span>
              {{ $item['label'] }}
            </div>
          @endforeach
        </div>

        <div class="flex flex-col gap-y-0.5">
          <p class="px-2 py-1 text-[11px] font-semibold tracking-wide text-muted-soft uppercase">Collections</p>
          @foreach ($dashboard['collections'] as $collection)
            <div class="flex items-center gap-x-2.5 rounded-md p-2 text-[13px] font-medium text-body">
              <span class="h-2 w-2 rounded-full" style="background-color: {{ $collection['dot'] }}"></span>
              {{ $collection['name'] }}
            </div>
          @endforeach
        </div>
      </div>

      {{-- Main panel --}}
      <div class="min-w-0 flex-1 p-5 sm:p-7">
        <div class="mb-6 flex items-start justify-between gap-4">
          <div>
            <p class="text-lg font-semibold tracking-[-0.4px] text-ink sm:text-[22px]">{{ $dashboard['greeting'] }}</p>
            <p class="mt-0.5 text-[13px] text-muted">Here's what's happening across your account.</p>
          </div>
          <div class="shrink-0 rounded-md bg-primary px-3.5 py-2 text-[13px] font-semibold text-on-primary">+ New collection</div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
          @foreach ($dashboard['stats'] as $stat)
            <div class="rounded-lg bg-card p-3.5">
              <p class="text-xs font-medium text-muted">{{ $stat['label'] }}</p>
              <p class="mt-1 text-[22px] font-semibold tracking-[-0.5px] text-ink">{{ $stat['value'] }}</p>
              <p class="mt-0.5 text-[11px] font-medium text-success">{{ $stat['delta'] }}</p>
            </div>
          @endforeach
        </div>

        <p class="mb-3 text-sm font-semibold text-ink">Your collections</p>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          @foreach ($dashboard['cards'] as $card)
            <div class="overflow-hidden rounded-lg border border-hairline">
              <div class="h-16" style="background: repeating-linear-gradient(135deg, {{ $card['from'] }} 0px, {{ $card['from'] }} 9px, {{ $card['to'] }} 9px, {{ $card['to'] }} 18px)"></div>
              <div class="px-3 py-2.5">
                <p class="text-[13px] font-semibold text-ink">{{ $card['name'] }}</p>
                <p class="mt-0.5 text-xs text-muted">{{ $card['meta'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
