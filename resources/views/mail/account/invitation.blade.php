<x-mail::message>
# You have been invited to join {{ $accountName }}

You have been invited to join the account **{{ $accountName }}** on {{ config('app.name') }}.

<x-mail::button :url="$link">
Accept the invitation
</x-mail::button>

This invitation will expire in 7 days.

<x-mail::panel>
If you were not expecting this invitation, you can safely ignore this email.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>
