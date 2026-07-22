<x-mail::message>
# Your testimonial is live 🎉

{{ __('Hi :name,', ['name' => $firstName]) }}

{{ __('Thank you so, so much for your testimonial. It means the world to us.') }}

{{ __('It is now published and live on the :name homepage for every collector to see. We are hyper grateful to have you in the community. 🦫', ['name' => config('app.name')]) }}

<x-mail::button :url="route('marketing.testimonials.index', ['locale' => 'en'])">
{{ __('View it on the marketing site') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
