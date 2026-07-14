<div class="pointer-events-none fixed bottom-0 z-50 flex w-full flex-col items-end p-4 sm:p-6 rtl:items-start" role="status" aria-live="polite">
  {{-- Replace instead of morphing: morph would patch the previous toast in place and
       keep its Alpine state, so a toast that already auto-hid stays hidden and x-init
       never re-runs its timer. Replacing builds a fresh component on every response. --}}
  <div x-sync x-merge="replace" id="notifications" class="pointer-events-auto relative w-full max-w-xs transform transition duration-300 ease-in-out">
    @if ($message = Session::get('status'))
      <div x-data="{ show: true }" x-transition.duration.300ms x-show="show" x-init="setTimeout(() => (show = false), 3000)" x-transition:enter-start="translate-y-12 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave-end="scale-90 opacity-0" class="flex items-center gap-3 rounded-lg border border-success/20 bg-canvas p-4 text-success shadow-lg">
        <div class="flex-shrink-0">
          <x-lucide-check class="h-5 w-5 text-success" />
        </div>

        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium">
            {{ $message }}
          </p>
        </div>
      </div>
    @endif
  </div>
</div>
