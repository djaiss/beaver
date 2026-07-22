@use('Illuminate\Support\Js')

<x-app-layout>
  <x-slot:title>
    {{ $account->name }}
  </x-slot>

  @php
    $currentUser = auth()->user();

    /*
     * Built here rather than inline in the attribute: Blade does not compile
     * @js() inside a component tag attribute, and a plain echo would escape an
     * apostrophe into one that closes the JS string once the browser decodes
     * the attribute. Js::from escapes it for JavaScript instead.
     */
    $confirmAccount = 'return confirm('.Js::from('Delete this account and everything in it? This can not be undone.').')';
    $confirmUser = 'return confirm('.Js::from('Delete this user? This can not be undone.').')';

    $stats = [
      'Members' => $members->count(),
      'Collections' => $collectionCount,
      'Items tracked' => $itemCount,
    ];
  @endphp

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-4xl space-y-8">
      <a href="{{ route('instanceAdmin.accounts.index') }}" data-turbo="true" class="flex items-center gap-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
        @svg('lucide-arrow-left', 'size-4')
        Accounts & users
      </a>

      <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ $account->name }}</h1>
          <p class="mt-1 text-sm text-muted">Created {{ $account->created_at->isoFormat('ll') }}</p>
        </div>

        {{-- Deleting an account takes every collection, item and photo in it, and
             the page navigates away afterwards, so this asks natively rather than
             through alpine-ajax the way the in place forms elsewhere do. --}}
        <x-form
          method="delete"
          :action="route('instanceAdmin.accounts.destroy', $account->id)"
          :onsubmit="$confirmAccount"
        >
          <x-button.secondary type="submit">Delete account</x-button.secondary>
        </x-form>
      </div>

      <div class="grid grid-cols-3 gap-3">
        @foreach ($stats as $label => $value)
          <div class="rounded-lg border border-hairline bg-canvas p-4">
            <p class="text-xs font-medium tracking-wide text-muted-soft uppercase">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($value) }}</p>
          </div>
        @endforeach
      </div>

      {{-- Members --}}
      <x-box title="Users in this account" padding="p-0">
        @foreach ($members as $member)
          <div class="flex flex-col gap-3 border-b border-hairline-soft px-4 py-4 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3">
              {{-- Initials only: the avatar files are streamed through a route that
                   refuses users outside the caller's own account, and an instance
                   administrator is by design not a member here. --}}
              <x-avatar :name="$member->getFullName()" :size="32" class="size-9 text-xs" />
              <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-ink">
                  {{ $member->getFullName() }}
                  @if ($member->isInstanceAdministrator())
                    <x-badge>Instance admin</x-badge>
                  @endif
                </p>
                <p class="truncate text-xs text-muted">{{ $member->email }} · {{ ucfirst($member->role) }}</p>
              </div>
            </div>

            {{-- You cannot act on yourself here: revoking your own flag or deleting
                 your own user would lock you out of this panel. --}}
            @if ($member->id !== $currentUser->id)
              <div class="flex items-center gap-2">
                <x-form method="put" :action="route('instanceAdmin.users.administrator.update', $member->id)">
                  <input type="hidden" name="is_instance_administrator" value="{{ $member->isInstanceAdministrator() ? 0 : 1 }}" />
                  <x-button.secondary type="submit">
                    {{ $member->isInstanceAdministrator() ? 'Revoke admin' : 'Make admin' }}
                  </x-button.secondary>
                </x-form>

                <x-form
                  method="delete"
                  :action="route('instanceAdmin.users.destroy', $member->id)"
                  :onsubmit="$confirmUser"
                >
                  <x-button.secondary type="submit">Delete</x-button.secondary>
                </x-form>
              </div>
            @endif
          </div>
        @endforeach
      </x-box>

      {{-- Activity --}}
      <x-box title="Latest activity" padding="p-0">
        @forelse ($activity as $log)
          <div class="flex items-center justify-between gap-3 border-b border-hairline-soft px-4 py-3 last:border-b-0">
            <div class="min-w-0">
              <p class="truncate text-sm text-ink">{{ $log->getTranslatedDescription() }}</p>
              <p class="truncate text-xs text-muted">{{ $log->getUserName() }}</p>
            </div>
            <span class="shrink-0 text-xs text-muted-soft">{{ $log->created_at->diffForHumans() }}</span>
          </div>
        @empty
          <x-empty-state>
            <x-slot:icon>
              @svg('lucide-activity', 'size-5 text-muted')
            </x-slot>
            Nothing has happened in this account yet.
          </x-empty-state>
        @endforelse
      </x-box>

      <x-box title="Not supported yet">
        <ul class="space-y-2.5">
          @foreach (['Impersonate a user', 'Suspend an account', 'Give free access', 'Billing plan'] as $item)
            <li class="flex items-center justify-between text-sm text-muted">
              {{ $item }}
              <x-soon />
            </li>
          @endforeach
        </ul>
      </x-box>
    </div>
  </div>
</x-app-layout>
