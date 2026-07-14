<div id="recovery-codes" class="border-b border-hairline p-4">
  <div class="mb-4 flex items-center">
    <x-lucide-wrench class="h-5 w-5 text-muted" />
    <div class="ms-5 flex w-full items-center justify-between">
      <div>
        <p class="font-semibold text-ink">
          {{ __('Recovery codes') }}
        </p>
        <p class="text-xs text-muted">{{ __('Use these codes to access your account if you lose access to your authenticator app.') }}</p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-3 gap-2 rounded-lg bg-card p-4">
    @foreach ($recoveryCodes as $code)
      <div class="font-mono text-sm text-ink">{{ $code }}</div>
    @endforeach
  </div>
</div>
