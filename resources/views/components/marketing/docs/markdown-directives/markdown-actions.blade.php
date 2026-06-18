@php
  $markdownUrl = (string) ($options['url'] ?? '');
@endphp

<div
  class="llm mb-8 flex flex-wrap items-center gap-y-2 text-sm font-semibold text-slate-600 dark:text-slate-300"
  x-data="{
    copied: false,
    copyFailed: false,
    markdownUrl: @js($markdownUrl),
    async copyMarkdown() {
      this.copyFailed = false

      try {
        const response = await fetch(this.markdownUrl, {
          headers: { Accept: 'text/plain' },
        })

        if (! response.ok) {
          throw new Error('Unable to load Markdown')
        }

        const markdown = await response.text()

        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(markdown)
        } else {
          const textarea = document.createElement('textarea')
          textarea.value = markdown
          textarea.style.position = 'fixed'
          textarea.style.opacity = '0'
          document.body.appendChild(textarea)
          textarea.select()
          document.execCommand('copy')
          textarea.remove()
        }

        this.copied = true
        setTimeout(() => (this.copied = false), 2000)
      } catch {
        this.copyFailed = true
      }
    },
  }">
  {!! $content !!}
</div>
