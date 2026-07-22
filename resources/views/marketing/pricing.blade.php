{{--
  The marketing pricing page. Like the homepage, all copy is hardcoded here on purpose so it
  lives next to the markup it belongs to. Strings are plain English for now (no __()); wrap them
  when the marketing site gets translated.

  The centrepiece is the Suspiciously Accurate Pricing Calculator: a fully interactive set of sliders,
  toggles and options whose itemized estimate always, deliberately, resolves to $49. The logic
  lives in resources/js/components/pricing-calculator.js.
--}}

<x-marketing-layout>
  {{-- Scoped styling for the calculator: the range sliders and the little pop on the total.
       The var(--*) values are the theme tokens, so both respond to light and dark mode. --}}
  <style>
    input[type="range"].price-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 9999px;
        background: var(--hairline);
        outline: none;
    }
    input[type="range"].price-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        background: var(--ink);
        cursor: pointer;
        border: 2px solid var(--page);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    }
    input[type="range"].price-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        background: var(--ink);
        cursor: pointer;
        border: 2px solid var(--page);
    }
    @keyframes price-pop {
        0% { transform: scale(0.96); }
        60% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    .price-pop { animation: price-pop 0.3s ease; }
  </style>

  {{-- HERO --}}
  <section id="top" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 text-center sm:px-8 sm:pt-24">
    <div class="mb-7 inline-flex items-center gap-x-2 rounded-full bg-card py-1.5 pr-3.5 pl-1.5 text-[13px] font-semibold text-ink">
      <span class="rounded-full bg-primary px-2 py-[3px] text-[11px] text-on-primary">NO SUBSCRIPTION</span>
      Pay once. Own it forever.
    </div>

    <h1 class="mx-auto max-w-[840px] text-[32px] leading-[1.08] font-semibold tracking-[-1px] text-balance text-ink sm:text-5xl sm:tracking-[-1.5px] lg:text-[64px] lg:leading-[1.05] lg:tracking-[-2px]">
      Pricing so simple, it's almost unbelievable.
    </h1>

    <p class="mx-auto mt-6 max-w-[600px] text-[17px] leading-relaxed text-muted sm:text-[19px]">
      One price. One time. No monthly nibbling at your wallet. Or, and we genuinely mean this, host it yourself for free and pay us nothing. We won't come back asking for more.
    </p>
  </section>

  {{-- SELF-HOST FIRST. The honest recommendation, put ahead of the paid plan on purpose. --}}
  <section class="mx-auto mt-14 max-w-[1200px] px-5 sm:px-8">
    <div class="rounded-xl bg-[#101010] p-6 text-white sm:p-12">
      <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-12">
        <div>
          <div class="mb-5 inline-flex items-center gap-x-2 rounded-full bg-[#1a1a1a] px-3 py-1.5 text-xs font-semibold text-[#a1a1aa]">
            <span class="text-badge-emerald" aria-hidden="true">&bull;</span> HONESTLY, DO THIS ONE
          </div>

          <h2 class="text-[28px] leading-[1.12] font-semibold tracking-[-1px] sm:text-4xl">Self-host it. It's free, and we recommend it.</h2>
          <p class="mt-4.5 mb-6 text-base leading-relaxed text-[#a1a1aa]">
            Most billing pages bury the free option. We're putting ours first, because it's genuinely the best deal around. One Docker command and KolleK is running on your own hardware: your data, your rules, zero dollars. Genuinely good, if we say so ourselves.
          </p>

          <div class="mb-7 flex flex-col gap-y-3">
            @foreach ([
                'Free forever, not a trial or a tease',
                'One Docker command and you are up and running',
                'Your data on your hardware, always',
                'Same full app as the paid cloud',
            ] as $point)
              <div class="flex items-center gap-x-3 text-[15px] text-[#e5e7eb]">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#1a1a1a]">
                  <x-lucide-check class="h-[11px] w-[11px] text-badge-emerald" stroke-width="3" />
                </span>
                {{ $point }}
              </div>
            @endforeach
          </div>

          <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="inline-flex h-12 items-center gap-x-2.5 rounded-md bg-white px-5.5 text-[15px] font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">
            Read the self-host docs
          </a>
        </div>

        <div class="overflow-hidden rounded-lg bg-[#1a1a1a]">
          <div class="flex items-center gap-x-2 border-b border-[#242424] px-4 py-3">
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#333333]"></span>
            <span class="ml-2 font-mono text-xs text-[#6b7280]">terminal, get it running</span>
          </div>
          <div class="overflow-x-auto px-4.5 py-4.5 font-mono text-[13px] leading-relaxed whitespace-nowrap">
            <p class="text-[#6b7280]"># pull it, run it, done, $0 forever</p>
            <p><span class="text-badge-emerald">$</span> <span class="text-[#e5e7eb]">docker run -p 8080:8080 \</span></p>
            <p class="pl-4 text-[#e5e7eb]">-v kollek:/data ghcr.io/kollek/app</p>
            <p class="mt-2.5 text-[#6b7280]">&check; pulling image…</p>
            <p class="text-[#6b7280]">&check; starting the app…</p>
            <p class="mt-1.5 text-badge-emerald">&check; listening on http://localhost:8080</p>
            <p class="mt-2.5 text-[#6b7280]"># that's the whole invoice. seriously.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- TWO PLANS --}}
  <section id="buy" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
    <div class="mb-12 text-center">
      <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">Two ways in. Both fair.</h2>
      <p class="mx-auto mt-4.5 max-w-[520px] text-[17px] text-muted">
        Roll up your sleeves and self-host for free, or let us handle the ops for a single, one-time $49. No fine print, no surprises.
      </p>
    </div>

    <div class="mx-auto grid max-w-[840px] grid-cols-1 gap-6 md:grid-cols-2">
      <div class="flex flex-col rounded-lg border border-hairline bg-canvas p-8">
        <p class="text-[22px] font-semibold tracking-[-0.3px] text-ink">Self-host</p>
        <p class="mt-1.5 text-sm text-muted">The thrifty collector's choice.</p>
        <p class="mt-4 text-[34px] font-semibold tracking-[-1px] text-ink">$0<span class="text-[15px] font-medium text-muted"> &middot; forever</span></p>

        <div class="my-7 flex flex-col gap-y-3">
          @foreach ([
              '$0, forever',
              'One-command Docker deploy',
              'Full control of your data',
              'Unlimited everything',
              'Updates via docker pull',
          ] as $feature)
            <div class="flex items-center gap-x-2.5 text-[15px] text-body">
              <x-lucide-check class="h-4 w-4 shrink-0 text-ink" stroke-width="2.4" />
              {{ $feature }}
            </div>
          @endforeach
        </div>

        <div class="flex-1"></div>
        <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="flex h-11 items-center justify-center rounded-md border border-hairline bg-canvas text-sm font-semibold text-ink transition-colors hover:bg-sidebar">docker run &amp; go</a>
      </div>

      <div class="relative flex flex-col rounded-lg bg-[#101010] p-8 text-white">
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-badge-emerald px-3 py-[5px] text-[11px] font-bold tracking-[0.4px] text-[#08331f]">MOST POPULAR</span>

        <div class="flex items-center gap-x-2.5">
          <p class="text-[22px] font-semibold tracking-[-0.3px]">Cloud</p>
          <span class="rounded-full bg-[#1a1a1a] px-2 py-[3px] text-[11px] font-semibold text-badge-emerald">PAY ONCE</span>
        </div>
        <p class="mt-1.5 text-sm text-[#a1a1aa]">For collectors who'd rather not run servers.</p>
        <div class="mt-4 flex flex-wrap items-baseline gap-x-2">
          <p class="text-[34px] font-semibold tracking-[-1px]">$49<span class="text-[15px] font-medium text-[#a1a1aa]"> once</span></p>
          <span class="text-[13px] text-[#6b7280] line-through">$6/mo forever</span>
        </div>

        <div class="my-7 flex flex-col gap-y-3">
          @foreach ([
              'One payment, no renewals',
              'Lifetime managed hosting',
              'Automatic updates & backups',
              'Unlimited members & storage',
              '30-day money-back guarantee',
          ] as $feature)
            <div class="flex items-center gap-x-2.5 text-[15px] text-[#e5e7eb]">
              <x-lucide-check class="h-4 w-4 shrink-0 text-badge-emerald" stroke-width="2.4" />
              {{ $feature }}
            </div>
          @endforeach
        </div>

        <div class="flex-1"></div>
        @auth
          <a href="{{ route('dashboard.index') }}" class="flex h-11 items-center justify-center rounded-md bg-white text-sm font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">Go to your account</a>
        @else
          <a href="{{ route('register') }}" class="flex h-11 items-center justify-center rounded-md bg-white text-sm font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">Buy once, $49</a>
        @endauth
      </div>
    </div>

    <p class="mt-6 text-center text-[13px] text-muted-soft">No hidden fees. No auto-renewals. No surprise charges on your credit card.</p>
  </section>

  {{-- PRICING CALCULATOR. Every input is real; the total is engineered to stay $49. --}}
  <section id="calculator" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24" x-data="pricingCalculator()">
    @php
        $sliders = [
            ['key' => 'items', 'label' => 'Items in your collection', 'hint' => 'The serious one. Really weigh this.', 'min' => 0, 'max' => 100000, 'step' => 100, 'display' => 'itemsDisplay'],
            ['key' => 'collections', 'label' => 'Number of collections', 'hint' => 'Books, wine, that box of cables, all of it.', 'min' => 1, 'max' => 500, 'step' => 1, 'display' => 'fmt(collections)'],
            ['key' => 'members', 'label' => 'Team members', 'hint' => 'Fellow collectors sharing the workspace.', 'min' => 1, 'max' => 250, 'step' => 1, 'display' => 'fmt(members)'],
            ['key' => 'storage', 'label' => 'Storage needed (GB)', 'hint' => 'For all those high-res cover scans.', 'min' => 1, 'max' => 2000, 'step' => 1, 'display' => 'storageDisplay'],
            ['key' => 'chaos', 'label' => 'Current shelf chaos level', 'hint' => '0 = librarian. 100 = archaeological dig.', 'min' => 0, 'max' => 100, 'step' => 1, 'display' => "chaos + '%'"],
            ['key' => 'giveups', 'label' => 'Times you alphabetized then gave up', 'hint' => 'Be honest. Nobody is judging. Much.', 'min' => 0, 'max' => 50, 'step' => 1, 'display' => 'giveupsDisplay'],
            ['key' => 'raccoons', 'label' => 'Raccoons infiltrating the collection', 'hint' => 'A known threat to any well-organized shelf.', 'min' => 0, 'max' => 12, 'step' => 1, 'display' => 'raccoonsDisplay'],
        ];

        $toggles = [
            ['key' => 'cloudBackups', 'label' => 'Encrypted daily backups', 'hint' => 'A genuinely good idea. Included regardless.'],
            ['key' => 'countsShelf', 'label' => 'You count your shelf as furniture', 'hint' => 'Structurally load-bearing books qualify.'],
            ['key' => 'labelMaker', 'label' => 'You own a label maker', 'hint' => 'And, be honest, you love it.'],
            ['key' => 'namedItems', 'label' => 'You have named individual items', 'hint' => 'Greg the first-edition. We know.'],
        ];

        $enthusiasms = ['Casual', 'Keen', 'Rabid', 'Unhinged'];
    @endphp

    <div class="mb-12 text-center">
      <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">The Suspiciously Accurate Pricing Calculator&trade;</p>
      <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">Configure your quote.</h2>
      <p class="mx-auto mt-4.5 max-w-[560px] text-[17px] text-muted">
        Our proprietary algorithm weighs {{ count($sliders) + count($toggles) + 1 }} rigorous factors to calculate your exact, personalized price. Drag away. We'll wait.
      </p>
    </div>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[1.35fr_1fr]">
      {{-- Inputs --}}
      <div class="flex flex-col gap-y-6 rounded-xl bg-card p-6 sm:p-8">
        @foreach ($sliders as $slider)
          <div>
            <div class="mb-3 flex items-baseline justify-between gap-3">
              <div>
                <p class="text-[15px] font-semibold text-ink">{{ $slider['label'] }}</p>
                <p class="mt-0.5 text-xs text-muted-soft">{{ $slider['hint'] }}</p>
              </div>
              <span class="shrink-0 rounded-md border border-hairline bg-page px-2.5 py-1 font-mono text-[13px] font-medium text-body whitespace-nowrap" x-text="{{ $slider['display'] }}"></span>
            </div>
            <input type="range" class="price-slider" min="{{ $slider['min'] }}" max="{{ $slider['max'] }}" step="{{ $slider['step'] }}" x-model.number="{{ $slider['key'] }}" />
          </div>
        @endforeach

        <div class="h-px bg-hairline"></div>

        <div class="flex flex-col gap-y-4">
          @foreach ($toggles as $toggle)
            <button type="button" @click="{{ $toggle['key'] }} = ! {{ $toggle['key'] }}" class="flex items-center justify-between gap-4 text-left">
              <span>
                <span class="block text-[15px] font-semibold text-ink">{{ $toggle['label'] }}</span>
                <span class="mt-0.5 block text-xs text-muted-soft">{{ $toggle['hint'] }}</span>
              </span>
              <span class="relative h-6 w-11 shrink-0 rounded-full transition-colors" :class="{{ $toggle['key'] }} ? 'bg-ink' : 'bg-hairline'">
                <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform" :class="{{ $toggle['key'] }} ? 'translate-x-[22px]' : ''"></span>
              </span>
            </button>
          @endforeach
        </div>

        <div class="h-px bg-hairline"></div>

        <div>
          <p class="text-[15px] font-semibold text-ink">Level of collecting enthusiasm</p>
          <p class="mt-0.5 mb-3 text-xs text-muted-soft">This dramatically affects nothing.</p>
          <div class="flex flex-wrap gap-2">
            @foreach ($enthusiasms as $enthusiasm)
              <button
                type="button"
                @click="enthusiasm = '{{ $enthusiasm }}'"
                :class="enthusiasm === '{{ $enthusiasm }}' ? 'border-primary bg-primary text-on-primary' : 'border-hairline bg-canvas text-ink'"
                class="rounded-md border px-3.5 py-2 text-[13px] font-semibold transition-colors"
              >{{ $enthusiasm }}</button>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Quote panel --}}
      <div class="sticky top-24 overflow-hidden rounded-xl border border-hairline bg-canvas shadow-[0_12px_40px_rgba(17,17,17,0.08)]">
        <div class="border-b border-hairline-soft px-7 py-5">
          <p class="text-xs font-semibold tracking-[0.5px] text-muted-soft uppercase">Your itemized estimate</p>
          <p class="mt-0.5 text-xs text-muted-soft">Recalculated live, with real math (trust us).</p>
        </div>

        <div class="flex flex-col px-7 py-2">
          <template x-for="item in lineItems" :key="item.label">
            <div class="flex items-center justify-between gap-3 border-b border-hairline-soft py-2.5">
              <span class="min-w-0 text-[13px] text-body" x-text="item.label"></span>
              <span class="shrink-0 font-mono text-[13px] whitespace-nowrap text-muted-soft" x-text="item.value"></span>
            </div>
          </template>

          <div class="flex items-center justify-between gap-3 pt-3.5 pb-1">
            <span class="text-[13px] font-semibold text-body">Subtotal</span>
            <span class="font-mono text-[13px] text-body">$49.00</span>
          </div>
          <div class="flex items-center justify-between gap-3 pt-1 pb-3.5">
            <span class="text-[13px] text-muted-soft">Complexity surcharge</span>
            <span class="font-mono text-[13px] text-badge-emerald">&ndash;$0.00</span>
          </div>
        </div>

        <div class="bg-[#101010] px-7 py-7 text-white">
          <div class="flex items-baseline justify-between">
            <span class="text-sm font-medium text-[#a1a1aa]">Your total, one time</span>
            <span class="price-pop text-[40px] font-semibold tracking-[-1.5px]" x-effect="pop($el)">$49</span>
          </div>
          <p class="mt-1.5 text-xs text-[#6b7280]" x-text="quip"></p>
          <a href="#buy" class="mt-5 flex h-[46px] items-center justify-center rounded-md bg-white text-sm font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">Lock in this incredible rate</a>
          <button type="button" @click="reset()" class="mt-3 w-full text-center text-xs text-[#a1a1aa] transition-colors hover:text-white">Reset the abacus</button>
        </div>
      </div>
    </div>

    <p class="mx-auto mt-5 max-w-[640px] text-center text-xs text-muted-soft">
      * The Suspiciously Accurate Pricing Calculator&trade; is fully functional and has never once returned a price other than $49. This is not a bug. It is our entire business model.
    </p>
  </section>

  {{-- REASSURANCE --}}
  <section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
      @foreach ([
          [
              'radius' => 'rounded-[4px]',
              'title' => 'No monthly nibbling',
              'desc' => "Subscriptions nibble away a little every month until there's nothing left. We charge once and then get out of your way.",
          ],
          [
              'radius' => 'rounded-full',
              'title' => 'Portable either way',
              'desc' => 'Self-host or cloud, your catalog exports to open JSON in one click. Leave whenever you like, the door is always open.',
          ],
          [
              'radius' => 'rounded-[3px]',
              'title' => 'Refund, no hard feelings',
              'desc' => "30 days to change your mind. If KolleK isn't for you, we return the $49 and part as friends.",
          ],
      ] as $card)
        <div class="flex flex-col gap-y-3.5 rounded-lg bg-card p-8">
          <span class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-primary">
            <span class="h-4 w-4 {{ $card['radius'] }} bg-on-primary"></span>
          </span>
          <p class="text-[20px] font-semibold tracking-[-0.3px] text-ink">{{ $card['title'] }}</p>
          <p class="text-[15px] leading-relaxed text-muted">{{ $card['desc'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- $49 IN PERSPECTIVE. The pun-filled comparison grid: what else forty-nine dollars buys,
       each card badged with an emoji. Every equivalence is deliberately, cheerfully approximate. --}}
  <section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
    @php
        $comparisons = [
            ['emoji' => '☕', 'eq' => '≈ 12.25×', 'thing' => '4 fancy oat-milk lattes', 'note' => 'Gone in an afternoon. KolleK is gone... never. It just stays and helps.'],
            ['emoji' => '🥐', 'eq' => '≈ 3×', 'thing' => '16 chocolatines', 'note' => 'Roughly $3 each at the good bakery. Flakier than a subscription, at least.'],
            ['emoji' => '🏋️', 'eq' => '≈ 0.7×', 'thing' => 'One month of that gym', 'note' => "You know the one. You've been twice. KolleK is the app you'll actually use."],
            ['emoji' => '🎮', 'eq' => '≈ 1×', 'thing' => 'A single video-game skin', 'note' => "For a character you'll retire next season. This organizes real treasure."],
            ['emoji' => '🚕', 'eq' => '≈ 6.5×', 'thing' => '7 rideshare surge fares', 'note' => 'One rainy Friday night, basically. KolleK never surge-prices at 2am.'],
            ['emoji' => '🔌', 'eq' => '≈ 0.25×', 'thing' => 'That impulse cable purchase', 'note' => 'The mystery HDMI drawer thanks you. So does a real catalog.'],
            ['emoji' => '🍿', 'eq' => '≈ 2×', 'thing' => '2 movie tickets + popcorn', 'note' => 'Two hours of entertainment vs. a lifetime of tidy shelves. Bold trade.'],
            ['emoji' => '♾️', 'eq' => '≈ ∞×', 'thing' => 'Zero monthly renewals', 'note' => 'The one comparison that matters: you pay it once and never again.'],
        ];
    @endphp

    <div class="mb-12 text-center">
      <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">$49 in perspective</p>
      <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">What else is forty-nine bucks?</h2>
      <p class="mx-auto mt-4.5 max-w-[560px] text-[17px] text-muted">
        To help you feel good about it, here's exactly what $49 buys elsewhere. KolleK is the only one that also organizes your entire life's collection. Just saying.
      </p>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
      @foreach ($comparisons as $comparison)
        <div class="relative flex min-h-[170px] flex-col gap-2.5 rounded-[14px] bg-card p-6 transition-shadow hover:shadow-[0_8px_20px_rgba(17,17,17,0.07)]">
          <span class="absolute top-4 right-4 flex h-[38px] w-[38px] items-center justify-center rounded-[10px] border border-hairline bg-page text-[19px] leading-none" aria-hidden="true">{{ $comparison['emoji'] }}</span>
          <p class="font-mono text-[13px] font-medium text-muted-soft">{{ $comparison['eq'] }}</p>
          <p class="text-[19px] leading-[1.2] font-semibold tracking-[-0.4px] text-ink">{{ $comparison['thing'] }}</p>
          <div class="flex-1"></div>
          <p class="text-[13px] leading-[1.5] text-muted">{{ $comparison['note'] }}</p>
        </div>
      @endforeach
    </div>

    <p class="mt-6 text-center text-[13px] text-muted-soft">Prices approximate, sourced from vibes and one very expensive coffee habit. KolleK, however, is exactly $49.</p>
  </section>

  {{-- FAQ --}}
  <section id="faq" class="mx-auto max-w-[760px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
    <h2 class="text-center text-[28px] leading-[1.15] font-semibold tracking-[-1px] text-ink sm:text-4xl">Frequently Asked Quest-ions.</h2>
    <p class="mt-3 mb-10 text-center text-base text-muted">Everything you're wondering about, answered.</p>

    <div class="flex flex-col border-t border-hairline" x-data="{ open: null }">
      @foreach ([
          [
              'question' => 'Wait, is it really just $49, one time?',
              'answer' => "Yes. Forty-nine dollars, paid once, and the cloud-hosted KolleK is yours. No monthly fee, no annual renewal, no surprise 'loyalty' price hikes. We only charge you the one time, and then we leave your wallet a-loan.",
          ],
          [
              'question' => 'Should I self-host instead?',
              'answer' => "Honestly? If you're even a little bit technical, yes. Self-hosting is free forever and takes one Docker command. You keep full control of your data and pay us nothing. We put the free option first on this page for a reason: it's the best deal, period.",
          ],
          [
              'question' => 'How do I self-host it?',
              'answer' => "Run 'docker run -p 8080:8080 -v kollek:/data ghcr.io/kollek/app' and open localhost:8080. That's the whole thing. Your data lives on a volume you own, updates are a docker pull away, and there's no invoice at the end.",
          ],
          [
              'question' => 'What does the $49 cloud plan actually cover?',
              'answer' => "Managed hosting for life, automatic updates, encrypted daily backups, and zero maintenance on your end. You collect; we keep the servers running. It's the same software as self-host, you're just paying us to handle the infrastructure for you.",
          ],
          [
              'question' => 'Is there a subscription hiding somewhere?',
              'answer' => "Not a chance. We built a whole pricing page and couldn't find one either. There is no per-seat fee, no storage tier, no 'premium' upsell. One payment, everything included, forever.",
          ],
          [
              'question' => 'Why does the calculator always say $49?',
              'answer' => 'Because the price is always $49. We could have hidden that behind fancy tiers and sliders, so we did build the sliders, purely for your entertainment. Drag them all you like; the total is engineered to remain gloriously, stubbornly $49.',
          ],
          [
              'question' => 'Can I get a refund if I change my mind?',
              'answer' => "Yes, a 30-day, no-questions-asked, no-hard-feelings refund. If KolleK isn't your cup of tea, we'll return your $49 and wish you well. We'd rather part as friends than hold a grudge.",
          ],
          [
              'question' => 'Do you offer a discount for teams, students, or very good collectors?',
              'answer' => "The price is already $49 one time with unlimited members, so a team of one pays the same as a team of thirty. That's about as discounted as flat gets. Students and very good collectors: you were already getting the deal.",
          ],
      ] as $index => $faq)
        <div class="border-b border-hairline">
          <button
            type="button"
            @click="open = open === {{ $index }} ? null : {{ $index }}"
            x-bind:aria-expanded="open === {{ $index }} ? 'true' : 'false'"
            class="flex w-full items-center justify-between gap-4 py-5.5 text-left"
          >
            <span class="text-[17px] font-semibold text-ink">{{ $faq['question'] }}</span>
            <span
              class="flex h-6 w-6 shrink-0 items-center justify-center text-xl text-muted transition-transform duration-200"
              x-bind:class="open === {{ $index }} ? 'rotate-45' : ''"
              aria-hidden="true"
            >+</span>
          </button>

          <div x-show="open === {{ $index }}" x-cloak>
            <p class="pr-11 pb-6 pl-1 text-[15px] leading-relaxed text-muted">{{ $faq['answer'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- FINAL CTA --}}
  <section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
    <div class="rounded-xl bg-card px-6 py-14 text-center sm:px-12 sm:py-18">
      <h2 class="mx-auto max-w-[620px] text-[28px] leading-[1.15] font-semibold tracking-[-1px] text-balance text-ink sm:text-[40px]">
        Stop putting it off. Start collecting.
      </h2>
      <p class="mx-auto mt-4.5 max-w-[460px] text-[17px] text-muted">
        Free if you self-host, $49 once if you'd like us to handle the hosting. Either way, no subscription is ever going to eat into your bank account.
      </p>

      <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
        @auth
          <a href="{{ route('dashboard.index') }}" class="flex h-12 items-center justify-center rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">Go to your account</a>
        @else
          <a href="{{ route('register') }}" class="flex h-12 items-center justify-center rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">Get started</a>
        @endauth

        <a href="#calculator" class="flex h-12 items-center justify-center gap-x-2 rounded-md border border-hairline bg-canvas px-5.5 text-[15px] font-semibold text-ink transition-colors hover:bg-sidebar">Re-run the calculator</a>
      </div>
    </div>
  </section>
</x-marketing-layout>
