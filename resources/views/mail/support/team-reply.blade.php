<x-mail::message>
# You have a new reply from support

The support team replied to your conversation **"{{ $subject }}"** on {{ config('app.name') }}.

<x-mail::panel>
{{ $reply }}
</x-mail::panel>

<x-mail::button :url="$link">
View the conversation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
