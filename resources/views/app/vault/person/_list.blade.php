<div class="flex h-[calc(100vh-48px)] flex-col overflow-hidden bg-white rounded-bl-lg rounded-tl-lg">
  <!-- Search header - fixed -->
  <form x-target="persons" action="{{ route('vault.person.search.create', [$vault->id]) }}" method="POST" class="shrink-0 border-b border-gray-200 p-3">
    @csrf
    @method('POST')

    <div class="relative">
      <x-phosphor-magnifying-glass class="pointer-events-none absolute top-1/2 left-2 h-4 w-4 -translate-y-1/2 text-gray-500" />
      <x-input @input.debounce="$el.form.requestSubmit()" id="term" type="text" placeholder="{{ __('app/person.list.search_placeholder') }}" class="w-full border border-gray-300 bg-gray-100 py-1 pr-3 pl-8 text-sm focus:bg-white" autocomplete="off" :error="$errors->get('term')" />
    </div>
  </form>

  <div class="shrink-0 border-b border-gray-200 p-3">
    <a href="{{ route('vault.person.new', [$vault->id]) }}" data-turbo="true" class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
      <x-phosphor-plus class="h-4 w-4" />
      {{ __('app/person.list.add') }}
    </a>
  </div>

  <!-- scrollable contact list -->
  <div class="overflow-y-auto">
    <div id="persons" class="divide-y divide-gray-200">
      @foreach ($persons as $currentPerson)
        <a href="{{ route('vault.person.show', [$vault->id, $currentPerson['slug']]) }}" data-turbo="true" class="{{ isset($person) && $person && $currentPerson['id'] === $person->id ? 'bg-blue-50' : '' }} flex cursor-pointer items-center gap-3 p-3 hover:bg-blue-50">
          <div class="shrink-0">x</div>
          <div class="min-w-0">
            <p class="truncate font-medium">{{ $currentPerson['name'] }}</p>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>
