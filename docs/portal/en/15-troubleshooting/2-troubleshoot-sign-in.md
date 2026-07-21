---
id: troubleshooting.signIn
title: Troubleshooting sign in
slug: troubleshoot-sign-in
section: troubleshooting
---

# Troubleshooting sign in

Locked out, or something on the sign in page is not doing what you expected? Find your symptom below. Each entry gives the fix first, then links to the fuller explanation.

## I forgot my password

Use the **forgot password** link on the sign in page. Enter your email, open the reset email, and choose a new password. The reset link expires after 60 minutes, so use it promptly, and request another if it lapses.

Faster alternative: request a @doc(auth.magicLinks, "magic link") instead. It signs you in without a password, and you can set a new password afterwards from your profile.

Full details in @doc(auth.resetPassword).

## My new password keeps being refused

KolleK requires at least eight characters and refuses any password that has appeared in a public data breach. The refusal is about the password itself, not your account. Pick something longer and unique that you have not used elsewhere. See @doc(auth.resetPassword).

## I lost my two factor device

At the two factor challenge, enter one of your **recovery codes** in place of the six digit code. Each recovery code works once. Once you are in, disable and re enable two factor authentication with your new device to get a fresh pairing and a fresh set of codes.

Full details in @doc(security.recoveryCodes).

:::warning
If you have lost your authenticator and have no recovery codes, there is no self serve way to complete the two factor step. On a self hosted instance, talk to whoever operates your server.
:::

## My magic link does not work

Magic links are valid for **five minutes** and work **once**. If yours expired or was already used, request a new one from the sign in page. Make sure you open the link on the device where you want to be signed in.

Full details in @doc(auth.magicLinks).

## I tried too many times and now I am blocked

Repeated rapid attempts are throttled to slow down password guessing. Wait a minute and try again, carefully. If you are unsure of the password, switch to the @doc(auth.resetPassword, "reset flow") or a @doc(auth.magicLinks, "magic link") rather than guessing on.

## I got a "failed sign in" email I do not recognize

Someone entered your email with a wrong password. See @doc(security.alertEmails) for what it means and when to act.

## My invitation link does not work

Two common causes:

- **The invitation expired.** Invitations last seven days. Ask the account owner to send a new one.
- **Your email already has a KolleK user.** A person belongs to exactly one account, so an invitation cannot be accepted by an email that already has an account of its own.

Full details in @doc(collaboration.invitePeople).

## The email I am waiting for never arrives

The reset email, the magic link, or the invitation may not be reaching you. That is usually a delivery problem rather than a sign in problem. See @doc(troubleshooting.emailDelivery).

## Where to next

- The basics of every sign in path: @doc(auth.signIn).
- Harden things once you are back in: @doc(security.index).
