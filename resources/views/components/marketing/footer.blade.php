<footer class="mt-24 bg-[#101010] text-[#a1a1aa]">
  <div class="mx-auto max-w-[1200px] px-5 py-16 sm:px-8">
    <div class="grid grid-cols-2 gap-8 border-b border-[#242424] pb-12 sm:grid-cols-3 lg:grid-cols-[1.6fr_1fr_1fr_1fr_1fr]">
      <div class="col-span-2 sm:col-span-3 lg:col-span-1">
        <div class="mb-4 flex items-center gap-x-2.5">
          <x-logo size="28" hoverColor="#ffffff" aria-hidden="true" />
          <x-wordmark height="17" class="text-white" />
        </div>
        <p class="max-w-60 text-sm leading-relaxed">{{ __('The open source collection manager that belongs to you.') }}</p>
      </div>

      @php
          $github = config('marketing.github_url');
          $columns = [
              [
                  'title' => __('Product'),
                  'links' => [
                      ['label' => __('Features'), 'url' => route('marketing.index') . '#features'],
                      ['label' => __('Roadmap'), 'url' => route('marketing.index') . '#roadmap'],
                      ['label' => __('Pricing'), 'url' => route('marketing.pricing.index')],
                  ],
              ],
              [
                  'title' => __('Resources'),
                  'links' => [
                      ['label' => __('Documentation'), 'url' => route('marketing.docs.portal.home.show')],
                      ['label' => __('API reference'), 'url' => route('marketing.docs.api.index')],
                      ['label' => __('FAQ'), 'url' => route('marketing.index') . '#faq'],
                      ['label' => __('Changelog'), 'url' => $github . '/releases'],
                  ],
              ],
              [
                  'title' => __('Community'),
                  'links' => [
                      ['label' => __('GitHub'), 'url' => $github],
                      ['label' => __('Discussions'), 'url' => $github . '/discussions'],
                      ['label' => __('Issues'), 'url' => $github . '/issues'],
                  ],
              ],
              [
                  'title' => __('Legal'),
                  'links' => [
                      ['label' => __('Privacy'), 'url' => '#'],
                      ['label' => __('Terms'), 'url' => '#'],
                      ['label' => __('MIT License'), 'url' => $github . '/blob/main/LICENSE'],
                  ],
              ],
          ];
      @endphp

      @foreach ($columns as $column)
        <div>
          <p class="mb-4 text-[13px] font-semibold text-white">{{ $column['title'] }}</p>
          <div class="flex flex-col gap-y-3">
            @foreach ($column['links'] as $link)
              <a href="{{ $link['url'] }}" class="text-sm text-[#a1a1aa] transition-colors hover:text-white">{{ $link['label'] }}</a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 pt-7">
      <p class="text-[13px] text-[#898989]">{{ __('© :year :name. Released under the MIT License.', ['year' => date('Y'), 'name' => config('app.name')]) }}</p>

      <div class="flex items-center gap-x-4">
        <x-theme-toggle class="border border-[#242424] text-[#a1a1aa] hover:bg-[#1a1a1a] hover:text-white" />

        <p class="flex items-center gap-x-2 text-[13px] text-[#898989]">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 9600 4800" aria-hidden="true">
            <title>{{ __('Flag of Canada') }}</title>
            <path fill="#f00" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z" />
            <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z" />
          </svg>
          {{ __('Made by collectors, for collectors. Proudly Canadian.') }}
        </p>
      </div>
    </div>
  </div>
</footer>
