@props(['section'])

@php
  $markdownUrl = route('marketing.docs.markdown.show', ['section' => $section['id']]);
@endphp

<div
  x-data="{
    copied: false,
    async copyForLlm() {
      const text = await (await fetch(@js($markdownUrl))).text();
      await navigator.clipboard.writeText(text);
      this.copied = true;
      setTimeout(() => (this.copied = false), 1500);
    },
  }"
  class="flex flex-wrap gap-2"
>
  <button
    type="button"
    @click="copyForLlm()"
    x-text="copied ? 'Copied!' : 'Copy for LLM'"
    class="rounded-md border border-gray-200 px-2.5 py-1.5 text-xs font-medium whitespace-nowrap text-gray-500 hover:border-gray-300 hover:text-gray-900"
  ></button>
  <a
    href="{{ $markdownUrl }}"
    target="_blank"
    class="rounded-md border border-gray-200 px-2.5 py-1.5 text-xs font-medium whitespace-nowrap text-gray-500 hover:border-gray-300 hover:text-gray-900"
  >View as Markdown</a>
</div>
