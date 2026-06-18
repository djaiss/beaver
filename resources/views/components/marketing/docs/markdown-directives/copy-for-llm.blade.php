<button type="button" class="inline-flex cursor-pointer items-center gap-1 pr-2 transition-colors hover:text-blue-600 focus-visible:text-blue-600 focus-visible:outline-none dark:hover:text-blue-400 dark:focus-visible:text-blue-400 sm:pr-3 text-xs" x-on:click="copyMarkdown" x-bind:title="copyFailed ? 'The Markdown could not be copied' : ''">
  <x-phosphor-check x-show="copied" x-cloak class="h-4 w-4" />
  <x-phosphor-clipboard-text x-show="!copied" class="h-4 w-4" />
  <span x-text="copied ? 'Copied' : copyFailed ? 'Copy failed' : 'Copy for LLM'">Copy for LLM</span>
</button>
