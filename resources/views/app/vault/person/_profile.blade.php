<div class="flex h-[calc(100vh-48px)] flex-col overflow-hidden bg-white">
  <!-- Contact header -->
  <div class="border-b border-gray-200 p-6">
    <!-- name + title + age -->
    <div id="profile-header" class="mb-6 flex items-center gap-4">
      <div class="h-16 w-16 shrink-0">
        <x-avatar name="{{ $person->name }}" size="64" />
      </div>
      <div class="flex min-w-0 flex-col gap-1">
        <!-- name -->
        <h1 class="truncate text-xl font-semibold">{{ $person->name }}</h1>
      </div>
    </div>

    <!-- Personal details -->
    <div class="space-y-2"></div>
  </div>

  <!-- Navigation menu -->
  <nav class="border-b border-gray-200">
    <div class="flex flex-col">
      <a href="" class="{{ request()->routeIs('vault.person.show') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-identification-card class="{{ request()->routeIs('vault.person.show') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('vault.person.show') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Overview') }}</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.life-event.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-heartbeat class="h-4 w-4 text-gray-500" />
        <span class="text-sm font-medium text-gray-600">{{ __('Life events') }}</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.note.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-notebook class="{{ request()->routeIs('person.note.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.note.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">Notes</span>
      </a>

      <a href="" class="{{ request()->routeIs('person.family.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-heart class="{{ request()->routeIs('person.family.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.family.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Love & family') }}</span>
      </a>

      <a href="" class="{{ request()->routeIs('person.reminder.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-bell class="{{ request()->routeIs('person.reminder.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.reminder.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Tasks and reminders') }}</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.gift.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-gift class="{{ request()->routeIs('person.gift.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.gift.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Gifts') }}</span>
      </a>
      <a href="#" class="group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-folder class="h-4 w-4 text-gray-500" />
        <span class="text-sm font-medium text-gray-600">Files</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.work.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-briefcase class="{{ request()->routeIs('person.work.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.work.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Work') }}</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.food.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-fork-knife class="{{ request()->routeIs('person.food.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.food.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Food') }}</span>
      </a>
      <a href="" class="{{ request()->routeIs('person.settings.index') ? 'border-blue-500 bg-blue-50' : '' }} group flex items-center gap-3 border-l-2 border-transparent px-4 py-3 hover:bg-gray-50">
        <x-phosphor-pencil-simple class="{{ request()->routeIs('person.settings.index') ? 'text-blue-500' : 'text-gray-500' }} h-4 w-4 transition-all duration-400 grouphover:-translate-y-0.5 group-hover:-rotate-3" />
        <span class="{{ request()->routeIs('person.settings.index') ? 'text-blue-700' : 'text-gray-600' }} text-sm font-medium">{{ __('Edit information') }}</span>
      </a>
    </div>
  </nav>
</div>
