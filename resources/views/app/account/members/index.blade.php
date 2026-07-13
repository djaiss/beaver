<x-app-layout>
  <x-slot:title>
    {{ __('Members') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Accounts'), 'route' => route('accounts.index')],
    ['label' => $account->name, 'route' => route('accounts.show', $account->id)],
    ['label' => __('Members')]
  ]" />

  @php
    $roleOptions = ['owner' => __('Owner'), 'editor' => __('Editor'), 'viewer' => __('Viewer')];
  @endphp

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-2xl items-start justify-center space-y-6">
      <x-box title="{{ __('Members') }}" padding="p-0">
        @foreach ($members as $accountMember)
          <div class="flex items-center justify-between gap-x-4 border-b border-gray-200 px-4 py-3 last:border-b-0 dark:border-gray-700">
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $accountMember->user->getFullName() }}</p>
              <p class="text-xs text-gray-500">{{ $accountMember->user->email }}</p>
            </div>

            <div class="flex items-center gap-x-2">
              <x-form method="put" :action="route('accounts.members.update', [$account->id, $accountMember->id])" class="flex items-center gap-x-2">
                <x-select id="role" :options="$roleOptions" :selected="$accountMember->role" />
                <x-button type="submit">{{ __('Update') }}</x-button>
              </x-form>

              <x-form method="delete" :action="route('accounts.members.destroy', [$account->id, $accountMember->id])">
                <x-button.secondary type="submit">{{ __('Remove') }}</x-button.secondary>
              </x-form>
            </div>
          </div>
        @endforeach
      </x-box>

      <x-box title="{{ __('Invite a member') }}">
        <x-form method="post" :action="route('accounts.members.create', $account->id)" class="space-y-4">
          <x-input id="email" name="email" :label="__('Email')" :error="$errors->get('email')" required placeholder="ross@friends.com" />
          <x-select id="role" :label="__('Role')" :options="$roleOptions" :selected="'viewer'" :error="$errors->get('role')" required />

          <div class="flex items-center justify-end">
            <x-button type="submit">{{ __('Send invitation') }}</x-button>
          </div>
        </x-form>
      </x-box>

      @if ($invitations->isNotEmpty())
        <x-box title="{{ __('Pending invitations') }}" padding="p-0">
          @foreach ($invitations as $invitation)
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 last:border-b-0 dark:border-gray-700">
              <p class="text-sm text-gray-900 dark:text-gray-100">{{ $invitation->email }}</p>
              <span class="text-xs text-gray-500">{{ __(ucfirst($invitation->role)) }}</span>
            </div>
          @endforeach
        </x-box>
      @endif
    </div>
  </div>
</x-app-layout>
