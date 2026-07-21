---
id: selfHosting.setupEmailDelivery
title: Set up email delivery
slug: set-up-email-delivery
section: self-hosting
---

# Set up email delivery

Email is how KolleK reaches people outside a browser session: [invitations](../8-collaboration/2-invite-people.md), [magic links](../10-security/4-magic-links.md), password resets, email verification, and [security alerts](../10-security/6-security-alert-emails.md) all arrive by email. Until you configure delivery, none of them go anywhere.

## The default sends nothing

A fresh instance ships with `MAIL_MAILER=log`. Every email is written to the application log file instead of being sent. This is deliberate: it means a half configured instance never silently sends mail from a wrong address, and you can read exactly what would have been sent while testing.

:::note
If someone says "I never got the invitation" on a new instance, this default is almost always why. The email exists, in the log file. See [Troubleshooting email delivery](../15-troubleshooting/3-troubleshoot-email-delivery.md).
:::

You have two supported ways to send real email: any SMTP server, or the Resend service.

## Option 1: SMTP

::::steps
:::step title="Set the mailer and server details"
In `.env`, set:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

Any transactional email provider or self run mail server with SMTP credentials works.
:::

:::step title="Set the sender identity"
Set the address and name your users will see:

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

Use a domain you control and have configured for sending (SPF and DKIM at your provider), or your mail will land in spam.
:::

:::step title="Apply and test"
Recreate the containers, then trigger a real email, for example by requesting a magic link from the sign in page:

```bash
docker compose up -d
```
:::
::::

## Option 2: Resend

If you use [Resend](https://resend.com), set:

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

Emails are then sent through Resend's API rather than SMTP, and each send records the Resend message id alongside it.

## Verifying delivery works

KolleK records every email it sends, per user, with its subject, body, and delivery status. After your test, check two places:

- Your inbox, for the obvious reason.
- The recipient's **sent emails** page in their profile, which lists what the instance sent them. See [Your personal activity log and sent emails](../9-account-and-profile/5-activity-log-and-sent-emails.md).

Common failure signs:

- **Nothing arrives and nothing errors.** The mailer is still `log`. Check `.env` was applied by recreating the containers.
- **Emails send but land in spam.** Sender domain is not authenticated. Configure SPF and DKIM with your provider.
- **Sending errors in the log.** Credentials or host details are wrong. The queue worker's logs contain the provider's error message.

Emails are sent by the background queue, so the **queue** container must be running for anything to leave the instance.

## Where to next

- Recognize the emails your instance sends in [Emails KolleK sends](../16-reference/4-emails-kollek-sends.md).
- Diagnose delivery problems in [Troubleshooting email delivery](../15-troubleshooting/3-troubleshoot-email-delivery.md).
