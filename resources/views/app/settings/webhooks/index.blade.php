<x-app-layout>
  <x-slot:title>
    {{ __('Webhooks') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Accounts'), 'route' => route('accounts.index')],
    ['label' => __('Webhooks')],
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include('app.settings._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <x-box padding="p-0">
          <x-slot:title>{{ __('Webhook endpoints') }}</x-slot>
          <x-slot:description>
            <p>{{ __('Webhooks let an external service receive a notification when something happens in your account.') }}</p>
            <p>{{ __('We sign every webhook with the endpoint\'s secret so the receiving service can verify the request came from us.') }}</p>
          </x-slot>

          <div id="new-webhook-form" class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800">
            <p class="text-sm text-zinc-500">{{ __('Your webhook endpoints') }}</p>

            <x-button.secondary href="{{ route('settings.webhooks.new') }}" x-target="new-webhook-form" class="mr-2 text-sm" data-test="new-webhook-button">
              {{ __('New webhook endpoint') }}
            </x-button.secondary>
          </div>

          <div id="webhook-list">
            @forelse ($endpoints as $endpoint)
              <div class="flex flex-col gap-3 border-b border-gray-200 p-3 first:border-t last:rounded-b-lg last:border-b-0 dark:border-gray-700">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-3">
                    <div class="rounded-sm bg-zinc-100 p-2 dark:bg-gray-800">
                      <x-phosphor-webhooks-logo class="h-4 w-4 text-zinc-500" />
                    </div>

                    <div class="flex flex-col">
                      <p class="text-sm font-semibold">{{ $endpoint->label ?? $endpoint->url }}</p>
                      <p class="font-mono text-xs break-all text-zinc-500">{{ $endpoint->url }}</p>
                    </div>
                  </div>

                  <x-form
                    x-target="webhook-list"
                    action="{{ route('settings.webhooks.destroy', $endpoint->id) }}"
                    method="delete"
                    x-on:ajax:before="
                    confirm('{{ __('Are you sure you want to proceed? This can not be undone.') }}') ||
                      $event.preventDefault()
                  ">
                    <x-button x-target="webhook-list" class="text-sm" data-test="delete-webhook-{{ $endpoint->id }}">
                      {{ __('Delete') }}
                    </x-button>
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
                  <span class="text-xs text-zinc-500">{{ __('Secret') }}</span>
                  <code class="flex-1 truncate rounded border border-gray-100 bg-gray-50 px-2 py-1 font-mono text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $endpoint->secret }}</code>
                  <button type="button" @click="copyToClipboard()" class="inline-flex cursor-pointer items-center rounded-md border border-gray-200 bg-white px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
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
              <p class="p-3 text-sm text-zinc-500">{{ __('No webhook endpoints created') }}</p>
            @endforelse
          </div>
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
