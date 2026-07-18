<section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
  <div
    class="grid grid-cols-1 items-center gap-8 rounded-xl bg-card p-6 sm:p-12 lg:grid-cols-2 lg:gap-12"
    x-data="{ active: 0, types: {{ Js::from($itemTypes) }} }"
  >
    <div>
      <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">Adapt to your hobby</p>
      <h2 class="text-[28px] leading-[1.15] font-semibold tracking-[-1px] text-ink sm:text-4xl">Every collection is different. Your fields should be too.</h2>
      <p class="mt-4.5 text-base leading-relaxed text-muted">
        Define completely custom item types with your own fields, sections, and metadata. A book isn't a bottle of wine, so don't force them into the same form.
      </p>

      <div class="mt-6 flex flex-wrap gap-2">
        @foreach ($itemTypes as $index => $type)
          <button
            type="button"
            @click="active = {{ $index }}"
            x-bind:class="active === {{ $index }} ? 'border-primary bg-primary text-on-primary' : 'border-hairline bg-canvas text-ink'"
            class="rounded-md border px-4 py-2 text-sm font-semibold transition-colors"
          >{{ $type['name'] }}</button>
        @endforeach
      </div>
    </div>

    <div class="rounded-lg border border-hairline bg-canvas p-6 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
      <div class="mb-4.5 flex items-center justify-between">
        <p class="text-[15px] font-semibold text-ink"><span x-text="types[active].name"></span> type</p>
        <span class="rounded-full bg-card px-2.5 py-1 text-xs font-medium text-body">Custom</span>
      </div>

      <div class="flex flex-col">
        <template x-for="field in types[active].fields" :key="field.name">
          <div class="flex items-center justify-between gap-3 border-b border-hairline-soft py-3">
            <div class="flex min-w-0 items-center gap-x-2.5">
              <span class="flex h-[26px] min-w-[26px] shrink-0 items-center justify-center rounded-sm bg-card px-1.5 font-mono text-[11px] text-muted" x-text="field.type"></span>
              <span class="truncate text-sm font-medium text-ink" x-text="field.name"></span>
            </div>
            <span class="shrink-0 font-mono text-xs text-muted-soft" x-text="field.sample"></span>
          </div>
        </template>

        <div class="flex items-center gap-x-2 pt-3.5 text-[13px] font-semibold text-body">
          <span class="flex h-[18px] w-[18px] items-center justify-center rounded-sm bg-card text-[13px] text-muted">+</span>
          Add field
        </div>
      </div>
    </div>
  </div>
</section>
