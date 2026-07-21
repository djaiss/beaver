# Emails KolleK sends

Every email the system can send, what triggers it, and who receives it. Use this page to recognize a legitimate message, or to verify delivery when you run an instance.

KolleK keeps a record of every email it sends you, including delivery and bounce status, on your [sent emails page](../account-and-profile/activity-log-and-sent-emails.md). Operators who have not configured a mailer yet should read [Set up email delivery](../self-hosting/set-up-email-delivery.md), because a fresh instance only logs email and sends nothing.

## Getting in and staying in

| Email | Triggered when | Sent to |
| --- | --- | --- |
| Account invitation | An owner invites someone to the account. The invitation link expires after seven days. | The invited address |
| Magic link | Someone requests a passwordless sign in link. The link is valid for five minutes. | The account email |
| Email verification | You register, or you change your email address. | The new address |
| Password reset | You use the forgot password link. The reset link is valid for 60 minutes. | The account email |

## Security alerts

These arrive unprompted when something notable happens on your account. See [Login and security alert emails](../security/security-alert-emails.md) for what to do when one surprises you.

| Email | Triggered when | Sent to |
| --- | --- | --- |
| Failed login alert | A password sign in attempt fails on an existing account. | The account email |
| New login alert | A successful sign in happens, naming the device used. | The account email |
| IP address change alert | A sign in comes from a different IP address than last time. | The account email |
| API key created | You create an API key by hand. Tokens created by signing in through the API do not trigger this notice. | The account email |
| API key deleted | You delete an API key. | The account email |

## Notices to the operator

These go to the operator address configured on the instance, not to collectors. They exist so whoever runs the server knows when people leave.

| Email | Triggered when | Sent to |
| --- | --- | --- |
| User deleted | A person deletes their own user, including the reason they gave. | The operator address |
| User automatically deleted | The system deletes a user who opted into inactivity deletion and has been inactive for six months. | The operator address |

## Where to next

- Recognize and react to the alerts: [Login and security alert emails](../security/security-alert-emails.md).
- Make email actually send on your instance: [Set up email delivery](../self-hosting/set-up-email-delivery.md).
- Check what was sent to you: [Your personal activity log and sent emails](../account-and-profile/activity-log-and-sent-emails.md).
