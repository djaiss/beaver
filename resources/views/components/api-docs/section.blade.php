@props(['section'])

<section id="{{ $section['id'] }}" class="grid scroll-mt-20 grid-cols-1 items-start gap-8 border-b border-gray-100 px-4 py-12 sm:px-8 lg:px-14 lg:py-20 xl:grid-cols-[minmax(220px,1fr)_minmax(280px,460px)] xl:gap-14">
  <div>
    <div class="mb-5 flex flex-wrap items-start justify-between gap-2.5">
      <p class="text-xs font-semibold tracking-wider text-blue-500 uppercase">{{ $section['kicker'] }}</p>
      <x-api-docs.markdown-actions :section="$section" />
    </div>

    <h2 class="mb-3 text-3xl font-semibold tracking-tight text-gray-900 lg:text-4xl">{{ $section['title'] }}</h2>
    <p class="max-w-xl text-base leading-relaxed text-gray-700">{{ $section['description'] }}</p>
    @foreach ($section['body'] as $paragraph)
      <p class="mt-4 max-w-xl text-base leading-relaxed text-gray-700">{{ $paragraph }}</p>
    @endforeach

    @if ($section['permissions'])
      <p class="mt-5 text-sm text-gray-500"><strong class="font-semibold text-gray-900">Permissions:</strong> {{ $section['permissions'] }}</p>
    @endif

    <x-api-docs.params title="Path parameters" :params="$section['pathParams']" />
    <x-api-docs.params title="Query parameters" :params="$section['queryParams']" />
    <x-api-docs.params title="Body parameters" :params="$section['bodyParams']" />

    @if ($section['returns'])
      <div class="mt-7">
        <p class="mb-2 text-base font-semibold text-gray-900">Returns</p>
        <p class="text-[15px] leading-relaxed text-gray-700">{{ $section['returns'] }}</p>
      </div>
    @endif
  </div>

  <div class="flex min-w-0 flex-col gap-4 xl:sticky xl:top-[88px]">
    <x-api-docs.code-panel :section="$section" />
    <x-api-docs.response-panel :section="$section" />
  </div>
</section>
