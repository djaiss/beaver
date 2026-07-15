@props(['section'])

<div
  x-data="{
    lang: 'curl',
    copied: false,
    samples: @js(collect($section['samples'])->map(fn (array $sample): string => $sample['code'])->all()),
    copy() {
      docsCopy(this.samples[this.lang]);
      this.copied = true;
      setTimeout(() => (this.copied = false), 1500);
    },
  }"
  class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50"
>
  <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 px-3 py-2.5">
    <x-api-docs.method-badge :method="$section['method']" class="mr-1" />
    @foreach ($section['samples'] as $key => $sample)
      <button
        type="button"
        @click="lang = '{{ $key }}'"
        :class="lang === '{{ $key }}' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900'"
        class="rounded-md px-2 py-1 text-[11px] font-semibold whitespace-nowrap"
      >{{ $sample['label'] }}</button>
    @endforeach
    <div class="min-w-2 flex-1"></div>
    <button type="button" @click="copy()" x-text="copied ? 'Copied!' : 'Copy'" class="px-1.5 py-1 text-[11px] font-semibold whitespace-nowrap text-gray-500 hover:text-gray-900"></button>
  </div>
  @foreach ($section['samples'] as $key => $sample)
    <pre
      x-show="lang === '{{ $key }}'"
      @if ($key !== 'curl') style="display: none" @endif
      class="overflow-x-auto p-4 font-mono text-[13px] leading-relaxed text-gray-700"
    >{!! $sample['html'] !!}</pre>
  @endforeach
</div>
