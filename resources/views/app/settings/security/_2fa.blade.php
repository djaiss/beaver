<x-box padding="p-0">
  <x-slot:title>{{ __('Two-factor authentication') }}</x-slot>
  <!-- Authenticator app -->
  <div id="authenticator-app" class="flex items-center p-3">
    <x-phosphor-device-mobile class="h-5 w-5 text-muted" />
    <div class="ms-5 flex w-full items-center justify-between">
      <div>
        <p class="font-semibold text-ink">
          {{ __('Authenticator app') }}
          @if ($has2fa)
            <x-badge color="success" class="ml-2">{{ __('Configured') }}</x-badge>
          @endif
        </p>
        <p class="text-xs text-muted">{{ __('Use an authentication app to get two-factor authentication codes when prompted.') }}</p>
      </div>

      @if ($has2fa)
        <x-form onsubmit="return confirm('{{ __('Are you absolutely sure? This action cannot be undone.') }}');" action="{{ route('profile.security.2fa.destroy') }}" method="delete">
          <x-button.secondary x-target="authenticator-app" class="mr-2 text-sm">
            {{ __('Remove') }}
          </x-button.secondary>
        </x-form>
      @else
        <x-button.secondary href="{{ route('profile.security.2fa.new') }}" x-target="authenticator-app" class="mr-2 text-sm">
          {{ __('Set up') }}
        </x-button.secondary>
      @endif
    </div>
  </div>

  <!-- recovery codes -->
  @if ($has2fa)
    <div id="recovery-codes" class="flex items-center border-t border-hairline-soft p-3">
      <x-phosphor-toolbox class="h-5 w-5 text-muted" />
      <div class="ms-5 flex w-full items-center justify-between">
        <div>
          <p class="font-semibold text-ink">
            {{ __('Recovery codes') }}
          </p>
          <p class="text-xs text-muted">{{ __('Use these codes to access your account if you lose access to your authenticator app.') }}</p>
        </div>

        <x-button.secondary turbo="true" href="{{ route('profile.security.recoverycodes.show') }}" x-target="recovery-codes" class="mr-2 text-sm">
          {{ __('Show') }}
        </x-button.secondary>
      </div>
    </div>
  @endif
</x-box>
