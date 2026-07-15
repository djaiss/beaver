@props(['section'])

<div x-data="{ copied: false }" class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
  <div class="flex items-center border-b border-gray-200 px-3.5 py-2.5">
    <span class="text-xs font-semibold text-gray-700">Response</span>
    <span class="ml-2 font-mono text-[11px] text-gray-400">{{ $section['responseStatus'] }}</span>
    <div class="flex-1"></div>
    @if ($section['responseJson'] !== null)
      <button
        type="button"
        @click="docsCopy(@js($section['responseJson'])); copied = true; setTimeout(() => (copied = false), 1500)"
        x-text="copied ? 'Copied!' : 'Copy'"
        class="px-1.5 py-1 text-[11px] font-semibold text-gray-500 hover:text-gray-900"
      ></button>
    @endif
  </div>
  @if ($section['responseJson'] === null)
    <p class="p-4 font-mono text-[13px] text-gray-400">No response body</p>
  @else
    <pre class="overflow-x-auto p-4 font-mono text-[13px] leading-relaxed text-gray-800">{!! $section['responseHtml'] !!}</pre>
  @endif
</div>
