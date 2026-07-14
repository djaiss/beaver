<x-app-layout>
  <x-slot:title>
    {{ __('Webhooks') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Webhooks') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Let external services receive a notification when something happens in your account.') }}</p>
      </div>

      <x-box padding="p-0">
        <x-slot:title>{{ __('Webhook endpoints') }}</x-slot>
        <x-slot:description>
          <p>{{ __('Webhooks let an external service receive a notification when something happens in your account.') }}</p>
          <p>{{ __('We sign every webhook with the endpoint\'s secret so the receiving service can verify the request came from us.') }}</p>
        </x-slot>

        <div id="new-webhook-form" class="flex items-center justify-between p-3">
          <p class="text-sm text-muted">{{ __('Your webhook endpoints') }}</p>

          <x-button.secondary href="{{ route('profile.webhooks.new') }}" x-target="new-webhook-form" class="mr-2 text-sm" data-test="new-webhook-button">
            {{ __('New webhook endpoint') }}
          </x-button.secondary>
        </div>

        <div id="webhook-list">
          @forelse ($endpoints as $endpoint)
            <div class="flex flex-col gap-3 border-b border-hairline-soft p-3 first:border-t last:border-b-0">
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                  <div class="rounded-sm bg-card p-2">
                    <x-phosphor-webhooks-logo class="h-4 w-4 text-muted" />
                  </div>

                  <div class="flex flex-col">
                    <p class="text-sm font-semibold text-ink">{{ $endpoint->label ?? $endpoint->url }}</p>
                    <p class="font-mono text-xs break-all text-muted">{{ $endpoint->url }}</p>
                  </div>
                </div>

                <x-form
                  x-target="webhook-list"
                  action="{{ route('profile.webhooks.destroy', $endpoint->id) }}"
                  method="delete"
                  x-on:ajax:before="
                  confirm('{{ __('Are you sure you want to proceed? This can not be undone.') }}') ||
                    $event.preventDefault()
                ">
                  <x-button.secondary x-target="webhook-list" class="text-sm" data-test="delete-webhook-{{ $endpoint->id }}">
                    {{ __('Delete') }}
                  </x-button.secondary>
                </x-form>
              </div>

              <!-- signing secret -->
              <div class="flex items-center gap-x-2" x-data="{
                copied: false,
                copyToClipboard() {
                  const el = document.createElement('textarea')
                  el.value = '{{ $endpoint->secret }}'
                  document.body.appendChild(el)
                  el.select()
                  document.execCommand('copy')
                  document.body.removeChild(el)

                  this.copied = true
                  setTimeout(() => {
                    this.copied = false
                  }, 2000)
                },
              }">
                <span class="text-xs text-muted">{{ __('Secret') }}</span>
                <code class="flex-1 truncate rounded border border-hairline bg-card px-2 py-1 font-mono text-xs text-body">{{ $endpoint->secret }}</code>
                <button type="button" @click="copyToClipboard()" class="inline-flex cursor-pointer items-center rounded-md border border-hairline bg-canvas px-2 py-1 text-xs font-semibold text-muted hover:bg-card">
                  <x-phosphor-check x-show="copied" class="mr-1 h-4 w-4" />
                  <x-phosphor-copy x-show="!copied" class="mr-1 h-4 w-4" />
                  <span x-text="
                    copied
                      ? '{{ __('Copied') }}'
                      : '{{ __('Copy') }}'
                  "></span>
                </button>
              </div>
            </div>
          @empty
            <p class="p-3 text-sm text-muted">{{ __('No webhook endpoints created') }}</p>
          @endforelse
        </div>
      </x-box>
    </div>
  </div>
</x-app-layout>
