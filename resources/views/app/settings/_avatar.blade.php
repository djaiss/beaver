<x-box helpId="profile.avatar">
  <x-slot:title>{{ __('Avatar') }}</x-slot>

  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div class="space-y-2">
      <p class="text-sm text-muted">{{ __('Your avatar is shown next to your name everywhere in the app.') }}</p>
      <p class="text-sm text-muted">{{ __('Use a square image for the best result, as avatars are displayed in a circle. JPEG, PNG and WebP are accepted, up to 5 MB.') }}</p>
      <p class="text-sm text-muted">{{ __('Without an avatar, we display your initials instead.') }}</p>
    </div>

    <div class="space-y-4">
      <div class="flex items-center gap-4">
        <x-avatar :user="$user" :size="96" class="size-24 text-2xl" />

        <div class="space-y-3">
          <x-form method="post" :action="route('profile.avatar.update')" :upload="true" class="space-y-3"
            x-data="{ sizeError: '' }"
            x-on:submit="if (sizeError) $event.preventDefault()"
          >
            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" required
              x-on:change="sizeError = window.oversizedFiles($event.target.files, 5120).length ? @js(__('The image must be under 5 MB.')) : ''"
              class="block w-full cursor-pointer text-sm text-muted file:mr-3 file:cursor-pointer file:rounded-md file:border file:border-hairline file:bg-canvas file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink" />

            <p x-show="sizeError" x-cloak x-text="sizeError" class="text-sm text-error"></p>
            <x-error :messages="$errors->get('avatar')" />

            <x-button>{{ __('Upload') }}</x-button>
          </x-form>

          @if($user->hasAvatar())
            <x-form method="delete" :action="route('profile.avatar.destroy')" onsubmit="return confirm('{{ __('Are you sure you want to remove your avatar? This can not be undone.') }}')">
              <button type="submit" class="cursor-pointer text-sm text-muted hover:text-ink">{{ __('Remove avatar') }}</button>
            </x-form>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-box>
