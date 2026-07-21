---
id: security.alertEmails
title: Login and security alert emails
slug: security-alert-emails
section: security
---

# Login and security alert emails

From time to time, KolleK may email you without you asking for anything. These alerts exist to make sure that when something happens around your user, you hear about it from KolleK before you hear about it any other way. This page lists each alert, what it means, and what to do if one surprises you.

## Failed sign in attempt

**When it arrives:** someone entered your email with a wrong password on the sign in page.

**If it was you**, mistyping your own password, ignore it.

**If it was not you**, someone is trying your address. One failed attempt is usually just noise, but repeated alerts mean your email is being targeted. Make sure your password is unique to KolleK, and turn on [two factor authentication](2-two-factor-authentication.md) so a guessed password is not enough.

## New sign in

**When it arrives:** a successful sign in happened, and the email names the device it came from.

**If it was you**, on a new browser, phone, or computer, ignore it.

**If it was not you**, someone has your password. [Change your password](5-reset-your-password.md) immediately, and review your account for anything unexpected.

## IP address change

**When it arrives:** you signed in from a different network address than last time.

This is normal when you travel, switch networks, or your provider rotates addresses. It only deserves attention if it arrives alongside a sign in you do not recognize.

## API key created, API key deleted

**When it arrives:** an [API key](7-manage-api-keys.md) was created or revoked on your user.

**If it was you**, managing your keys, ignore it.

**If it was not you**, treat it seriously. An unexpected key means someone had enough access to create one. Revoke the key, change your password, and check your remaining keys and their last used times.

:::note
Sign in tokens created when you log in through the API do not trigger the key created email. Only keys you create by hand do, so the alert stays meaningful.
:::

## Emails you asked for

Two other emails arrive only because someone requested them, so they are not alerts in themselves: the [magic link](4-magic-links.md) email, and the password reset email. If you receive one you did not request, someone entered your address in that form. Neither can be used without access to your inbox, but repeated unrequested emails are another sign your address is being poked at.

## If something genuinely looks wrong

1. [Change your password](5-reset-your-password.md).
2. Turn on [two factor authentication](2-two-factor-authentication.md) if it is off.
3. Review your [API keys](7-manage-api-keys.md) and revoke anything you do not recognize.
4. Check [your personal activity log](../9-account-and-profile/5-activity-log-and-sent-emails.md) for actions you did not take.

## Where to next

- See everything KolleK has ever sent you, with delivery status: [Your personal activity log and sent emails](../9-account-and-profile/5-activity-log-and-sent-emails.md).
- The full catalogue of every email KolleK can send: [Emails KolleK sends](../16-reference/4-emails-kollek-sends.md).
