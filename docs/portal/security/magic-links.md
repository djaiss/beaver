# Magic links explained

A magic link is a passwordless way to sign in. Instead of typing your password, you ask KolleK to email you a link. Open the link, and you are signed in. This page explains how it works, when it is convenient, and the one trade off you should understand before relying on it.

## Request a magic link

On the sign in page, choose the magic link option, enter your **email**, and submit. KolleK sends a one time link to that address. Open it, and you land on your dashboard.

For your privacy, the page shows the same confirmation whether or not an account exists for the address you entered, so it never reveals who is registered.

## The rules it follows

- **The link is valid for five minutes.** If it expires before you open it, request another. Nothing is lost.
- **It goes to your account email only.** You need access to that inbox. This is also what makes the link safe: only someone who can read your email can use it.
- **It works once.** A link that has signed you in cannot be reused.

## The trade off with two factor authentication

Signing in with a magic link does not ask for a [two factor](two-factor-authentication.md) code.

This is by design, not an oversight. A magic link already proves two things at once: that the person signing in knows your email address, and that they control the inbox behind it. The inbox is acting as the second factor.

:::warning
If you use two factor authentication, remember that anyone who controls your email inbox can sign in to KolleK with a magic link, without ever seeing your authenticator. Your email account is the real gate, so protect it with a strong password and its own two factor setup.
:::

## When to use it

Magic links suit you when:

- You are on a device where you do not want to type your password.
- You have forgotten your password and just need to get in. Once in, you can [set a new password](reset-your-password.md) from your profile.
- You prefer not to use a password day to day and your email account is well protected.

Prefer your password and authenticator code when you are on a shared or untrusted machine where you would rather not open your inbox at all.

## Where to next

- Every sign in path in one place: [Signing in](../getting-started/signing-in.md).
- Strengthen the front door: [Protect your account with two factor authentication](two-factor-authentication.md).
- Link never arrived? See [Troubleshooting email delivery](../troubleshooting/troubleshoot-email-delivery.md).
