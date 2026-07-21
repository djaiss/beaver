---
id: security.recoveryCodes
title: Save and use your recovery codes
slug: recovery-codes
section: security
---

# Save and use your recovery codes

Recovery codes are your way back in if you lose your authenticator. When you turn on [two factor authentication](2-two-factor-authentication.md), KolleK generates eight of them. Each code works exactly once, in place of a code from your app.

Phones get lost, broken, and replaced. Recovery codes are what stand between that ordinary bad day and being locked out of your catalogue.

## Where you get them

The codes are shown right after you confirm two factor setup. That moment is when you should save them.

Good places to keep them:

- A password manager, in the notes of your KolleK entry.
- A printed page in a drawer at home.
- An encrypted file you back up.

A bad place to keep them is your phone alone, because the situation where you need them is the situation where your phone is gone.

:::warning
If you lose both your authenticator and your recovery codes, you cannot complete the two factor step and you may be locked out of your user. There is no self serve way around it, so store the codes somewhere safe now.
:::

## Use a code to sign in

When KolleK asks for your six digit authenticator code and you cannot provide one:

1. At the two factor challenge, enter one of your recovery codes instead of the code from the app.
2. You are signed in as normal.

That is all there is to it. The challenge accepts either a current authenticator code or an unused recovery code.

## Each code works once

A recovery code is consumed the moment you use it. It will never work again, and your remaining codes stay valid. Cross used codes off wherever you stored them.

:::note
If you are running low on codes, or you suspect someone else has seen them, disable two factor authentication and turn it back on. Re enabling generates a fresh set of eight codes and invalidates the old ones.
:::

## After you are back in

If you used a recovery code because you lost your authenticator for good, take two minutes to reset things properly: disable two factor authentication from your security settings, then enable it again with your new device. You will get a new QR code to scan and a fresh set of recovery codes to save.

## Where to next

- Set up or reset the code step itself: [Protect your account with two factor authentication](2-two-factor-authentication.md).
- Locked out in some other way? See [Troubleshooting sign in](../15-troubleshooting/2-troubleshoot-sign-in.md).
