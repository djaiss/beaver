<section id="top" class="mx-auto max-w-[1200px] px-5 pt-16 text-center sm:px-8 sm:pt-24">
  <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="mb-7 inline-flex items-center gap-x-2 rounded-full bg-card py-1.5 pr-3.5 pl-1.5 text-[13px] font-medium text-body">
    <span class="rounded-full bg-primary px-2 py-[3px] text-[11px] font-semibold text-on-primary">MIT</span>
    Open source and self-hostable, forever
  </a>

  <h1 class="mx-auto max-w-[820px] text-[32px] leading-[1.08] font-semibold tracking-[-1px] text-balance text-ink sm:text-5xl sm:tracking-[-1.5px] lg:text-[64px] lg:leading-[1.05] lg:tracking-[-2px]">
    The collection manager that belongs to you.
  </h1>

  <p class="mx-auto mt-6 max-w-[600px] text-[17px] leading-relaxed text-muted sm:text-[19px]">
    Catalog comics, books, vinyl, trading cards, wine, watches, games, anything you collect. Own your data. Self-host or use the cloud.
  </p>

  <x-marketing.cta-buttons class="mt-9" />

  <div class="mt-6 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[13px] text-muted-soft">
    <span>&starf; {{ $githubStats[0]['value'] }} stars</span>
    <span aria-hidden="true">&middot;</span>
    <span>Pay once, own it forever</span>
    <span aria-hidden="true">&middot;</span>
    <span>No subscription</span>
  </div>
</section>
