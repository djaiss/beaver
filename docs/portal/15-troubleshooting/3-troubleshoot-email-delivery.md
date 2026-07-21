---
id: troubleshooting.emailDelivery
title: Troubleshooting email delivery
slug: troubleshoot-email-delivery
section: troubleshooting
---

# Troubleshooting email delivery

You invited someone and nothing arrived. You requested a magic link and your inbox stays empty. This page explains why expected emails go missing and how to find out what actually happened.

## The most common cause: a fresh instance does not send email

On a newly self hosted instance, KolleK's mailer defaults to **logging emails instead of sending them**. Every email is composed and recorded, but nothing leaves the server until an operator configures a real mail service.

This is deliberate, so an unconfigured instance never silently fails or accidentally spams. But it means that on a fresh install, invitations, magic links, password resets, and security alerts all appear to vanish.

:::note
If nobody has configured mail on your instance yet, no email will arrive, for anyone, ever. This is the first thing to check.
:::

**If you operate the instance**, set up SMTP or Resend by following [Set up email delivery](../14-self-hosting/4-set-up-email-delivery.md).

**If someone else operates it**, point them at that page. There is nothing you can change from inside the app.

## Check what was actually sent

KolleK records every email it sends to you, with its delivery status. Go to your profile and open your **sent emails** history. Each entry shows when it was sent, and where tracking is available, whether it was delivered or bounced.

How to read what you find:

- **The email is listed and marked delivered.** KolleK did its job. Check your spam folder, and search your inbox for the sender address.
- **The email is listed and marked bounced.** Your mail provider refused it. Check that your address is correct in your profile, and whether your provider is blocking the instance's sender.
- **The email is listed with no delivery information.** On instances that send through plain SMTP, delivery tracking is not available, so this is normal. Absence of a bounce is a good sign.
- **The email is not listed at all.** It was never composed, which usually means the action did not complete. Try the action again.

Full details on this screen in [Your personal activity log and sent emails](../9-account-and-profile/5-activity-log-and-sent-emails.md).

## An invitation never reached the invitee

The invitation email goes to the invitee, so it never appears in your own sent history. Ask the invitee to check spam, verify you typed their address correctly, and remember that invitations expire after seven days. If in doubt, send a fresh one. On a fresh instance, check the mailer configuration first, as above.

## Verification, resets, and magic links land in spam

Transactional email from a small self hosted instance is exactly what spam filters are suspicious of. Marking one message as "not spam" usually teaches your provider. Operators can improve deliverability with proper sender configuration, covered in [Set up email delivery](../14-self-hosting/4-set-up-email-delivery.md).

## Where to next

- Operator setup for real delivery: [Set up email delivery](../14-self-hosting/4-set-up-email-delivery.md).
- Your personal email history: [Your personal activity log and sent emails](../9-account-and-profile/5-activity-log-and-sent-emails.md).
- What each email is and when it fires: [Emails KolleK sends](../16-reference/4-emails-kollek-sends.md).
