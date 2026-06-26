<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - Login form -->
    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full space-y-8">
        @if (config('app.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="{{ route('marketing.index') }}" class="group-hover:underline">{{ __('app/auth.shared.back_to_marketing') }}</x-link>
          </p>
        @endif

        <!-- Title -->
        <div>
          <div class="mb-2 flex items-center gap-x-2">
            <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
              <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
                <x-image src="{{ asset('images/marketing/logo/30x30.webp') }}" srcset="{{ asset('images/marketing/logo/30x30.webp') }} 1x, {{ asset('images/marketing/logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="{{ config('app.name') }} logo" />
              </div>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('app/auth.magic_link.title') }}</h1>
          </div>
          <p class="text-sm text-gray-500">{{ __('app/auth.magic_link.subtitle') }}</p>
        </div>

        <!-- Login form -->
        <x-box>
          <x-form method="post" :action="route('magic.link.store')" class="space-y-4">
            <!-- Email address -->
            <x-input type="email" id="email" value="{{ old('email') }}" :label="__('app/auth.shared.email_address')" required placeholder="john@doe.com" :error="$errors->get('email')" :passManagerDisabled="false" autocomplete="username" autofocus />

            <div class="flex items-center justify-between">
              <x-button data-test="send-button">{{ __('app/auth.magic_link.submit') }}</x-button>
            </div>
          </x-form>
        </x-box>

        <!-- Register link -->
        <x-box class="text-center text-sm">
          {{ __('app/auth.shared.use_password_instead') }}
          <x-link :href="route('login')" class="ml-1">{{ __('app/shared.back_to_login') }}</x-link>
        </x-box>

        <ul class="text-xs text-gray-600">
          <li>© {{ config('app.name') }} {{ now()->format('Y') }}</li>
        </ul>
      </div>
    </div>

    <!-- Right side -->
    @include ('partials.quotes', ['quote' => $quote])
  </div>
</x-guest-layout>
