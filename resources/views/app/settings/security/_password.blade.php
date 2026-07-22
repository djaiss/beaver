<x-box padding="p-0" helpId="profile.password">
  <x-slot:title>{{ __('Change password') }}</x-slot>

  <x-form id="password-form" x-target="password-form" x-merge="replace" method="put" action="{{ route('profile.security.password.update') }}">
    <!-- current password -->
    <div class="grid grid-cols-3 items-center border-b border-hairline-soft p-3">
      <p class="col-span-2 block text-sm font-medium text-ink">{{ __('Current password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="current_password" type="password" required :error="$errors->get('current_password')" autofocus />
      </div>
    </div>

    <!-- new password -->
    <div class="grid grid-cols-3 items-center border-b border-hairline-soft p-3">
      <p class="col-span-2 block text-sm font-medium text-ink">{{ __('New password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password" type="password" help="{{ __('Minimum 8 characters.') }}" passwordrules="minlength: 8" required :error="$errors->get('new_password')" :passManagerDisabled="false" />
      </div>
    </div>

    <!-- confirm new password -->
    <div class="grid grid-cols-3 items-center border-b border-hairline-soft p-3">
      <p class="col-span-2 block text-sm font-medium text-ink">{{ __('Confirm new password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password_confirmation" type="password" name="new_password_confirmation" required :error="$errors->get('new_password_confirmation')" />
      </div>
    </div>

    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
