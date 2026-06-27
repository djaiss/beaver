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
  <nav class="border-b border-gray-200"></nav>
</div>
