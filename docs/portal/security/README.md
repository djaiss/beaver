# Security overview

KolleK holds records that matter to you: what you own, what it is worth, and where it lives. This page maps out the controls that keep your user and your data safe, so you can decide which ones to turn on. All of them are optional. Most of them are worth five minutes of your time.

## Your password

Every account starts with a password. KolleK enforces two rules when you set one: it must be at least eight characters, and it is checked against lists of passwords known to have leaked in past breaches. If a password you try is refused, it is because it appeared in one of those lists, so pick something you have not used elsewhere.

You can change your password at any time, and recover access if you forget it. See [Reset your password](reset-your-password.md).

## Two factor authentication

The single biggest upgrade you can make. With two factor authentication turned on, signing in with your password also asks for a six digit code from an authenticator app on your phone. A stolen password alone is no longer enough to get in.

Set it up in [Protect your account with two factor authentication](two-factor-authentication.md), and make sure you understand [recovery codes](recovery-codes.md) before you rely on it.

## Recovery codes

When you enable two factor authentication, KolleK gives you eight recovery codes. Each one can be used once, in place of an authenticator code, to get back in if you lose your phone. Store them somewhere safe. [Save and use your recovery codes](recovery-codes.md) explains how.

## Magic links

A passwordless way to sign in. KolleK emails you a link that signs you in directly, valid for five minutes. Convenient, with one trade off worth understanding: a magic link does not ask for a two factor code, because access to your inbox already acts as the second factor. [Magic links explained](magic-links.md) covers when to use them.

## API keys

If you use the KolleK API, you authenticate with personal API keys. They are created and revoked from your profile, and KolleK emails you whenever one is created or deleted, so a key you did not make never goes unnoticed. See [Manage API keys](manage-api-keys.md).

## Alert emails

KolleK watches for events worth telling you about: a failed sign in attempt, a sign in from a new device, a change in your IP address, an API key created or deleted. When one happens, you get an email. [Login and security alert emails](security-alert-emails.md) explains what each alert means and what to do about it.

## A sensible setup

If you only do two things, do these:

1. Turn on [two factor authentication](two-factor-authentication.md).
2. Save your [recovery codes](recovery-codes.md) somewhere that is not your phone.

Everything else in this section can wait until you need it.

## Pages in this section

1. [Protect your account with two factor authentication](two-factor-authentication.md)
2. [Save and use your recovery codes](recovery-codes.md)
3. [Magic links explained](magic-links.md)
4. [Reset your password](reset-your-password.md)
5. [Login and security alert emails](security-alert-emails.md)
6. [Manage API keys](manage-api-keys.md)
