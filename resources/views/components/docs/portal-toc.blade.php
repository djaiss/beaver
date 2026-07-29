@props(['toc'])

@if (! empty($toc))
  <div class="doc-scroll sticky top-[7.5rem] hidden h-[calc(100vh-7.5rem)] w-[236px] shrink-0 self-start overflow-y-auto py-11 pr-7 pl-2 xl:block">
    <p class="mb-3.5 text-[11px] font-semibold tracking-wider text-muted-soft uppercase">{{ __('On this page') }}</p>
    <div id="doc-toc" class="flex flex-col border-l border-hairline-soft">
      @foreach ($toc as $entry)
        <a
          href="#{{ $entry['id'] }}"
          data-toc="{{ $entry['id'] }}"
          @class([
              '-ml-px border-l-2 border-transparent py-1.5 text-[13px] leading-snug text-muted transition-colors hover:text-ink',
              'pl-4' => $entry['level'] === 2,
              'pl-7' => $entry['level'] === 3,
          ])
        >{{ $entry['text'] }}</a>
      @endforeach
    </div>
  </div>
@endif
