<section id="opensource" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="rounded-xl bg-[#101010] p-6 text-white sm:p-12 lg:p-16">
    <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-14">
      <div>
        <div class="mb-5 inline-flex items-center gap-x-2 rounded-full bg-[#1a1a1a] px-3 py-1.5 text-xs font-semibold text-[#a1a1aa]">
          <span class="text-badge-emerald" aria-hidden="true">&bull;</span> MIT LICENSED
        </div>

        <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">Open by design.</h2>
        <p class="mt-5 mb-7 text-[17px] leading-relaxed text-[#a1a1aa]">
          {{ config('app.name') }} is released under the MIT License, the whole source, no strings. Read it, fork it, ship it commercially, run it on your own hardware. No vendor lock-in, ever.
        </p>

        <div class="mb-8 flex flex-col gap-y-3">
          @foreach ($openSourcePoints as $point)
            <div class="flex items-center gap-x-3 text-[15px] text-[#e5e7eb]">
              <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#1a1a1a]">
                <x-lucide-check class="h-[11px] w-[11px] text-badge-emerald" stroke-width="3" />
              </span>
              {{ $point }}
            </div>
          @endforeach
        </div>

        <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="inline-flex h-12 items-center gap-x-2.5 rounded-md bg-white px-5.5 text-[15px] font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">
          <x-lucide-github class="h-[18px] w-[18px]" />
          Star on GitHub
        </a>
      </div>

      <div class="flex flex-col gap-4">
        <div class="grid grid-cols-3 gap-4">
          @foreach ($githubStats as $stat)
            <div class="rounded-lg bg-[#1a1a1a] p-5">
              <p class="text-2xl font-semibold tracking-[-1px] sm:text-3xl">{{ $stat['value'] }}</p>
              <p class="mt-1 text-[13px] text-[#a1a1aa]">{{ $stat['label'] }}</p>
            </div>
          @endforeach
        </div>

        <div class="overflow-hidden rounded-lg bg-[#1a1a1a]">
          <div class="flex items-center gap-x-2 border-b border-[#242424] px-4 py-3">
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="ml-2 font-mono text-xs text-[#6b7280]">terminal — self-host</span>
          </div>
          <div class="overflow-x-auto px-4.5 py-4.5 font-mono text-[13px] leading-relaxed whitespace-nowrap">
            <p class="text-[#6b7280]"># up and running in one command</p>
            <p><span class="text-badge-emerald">$</span> <span class="text-[#e5e7eb]">docker run -p 8000:8000 \</span></p>
            <p class="pl-4 text-[#e5e7eb]">-v {{ Str::lower(config('app.name')) }}:/data ghcr.io/djaiss/{{ Str::lower(config('app.name')) }}</p>
            <p class="mt-2 text-[#6b7280]">&check; listening on http://localhost:8000</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
