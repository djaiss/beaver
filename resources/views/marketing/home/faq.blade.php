<section id="faq" class="mx-auto max-w-[760px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
  <h2 class="mb-10 text-center text-[28px] leading-[1.15] font-semibold tracking-[-1px] text-ink sm:text-4xl">Questions, answered.</h2>

  <div class="flex flex-col border-t border-hairline" x-data="{ open: null }">
    @foreach ($faqs as $index => $faq)
      <div class="border-b border-hairline">
        <button
          type="button"
          @click="open = open === {{ $index }} ? null : {{ $index }}"
          x-bind:aria-expanded="open === {{ $index }} ? 'true' : 'false'"
          class="flex w-full items-center justify-between gap-4 py-5.5 text-left"
        >
          <span class="text-[17px] font-semibold text-ink">{{ $faq['question'] }}</span>
          <span
            class="flex h-6 w-6 shrink-0 items-center justify-center text-xl text-muted transition-transform duration-200"
            x-bind:class="open === {{ $index }} ? 'rotate-45' : ''"
            aria-hidden="true"
          >+</span>
        </button>

        <div x-show="open === {{ $index }}" x-cloak>
          <p class="pr-11 pb-6 pl-1 text-[15px] leading-relaxed text-muted">{{ $faq['answer'] }}</p>
        </div>
      </div>
    @endforeach
  </div>
</section>
