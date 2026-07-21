---
id: auth.resetPassword
title: Reset your password
slug: reset-your-password
section: security
---

# Reset your password

Whether you have forgotten your password or simply want a new one, this page covers both paths: recovering access from the sign in page, and changing your password deliberately from your profile.

## If you have forgotten your password

1. On the sign in page, choose the **forgot password** link.
2. Enter your email address and submit.
3. Open the email KolleK sends you and follow the reset link.
4. Choose a new password and confirm it. You can now sign in with it.

Two behaviors here are worth knowing so they do not confuse you:

- **The confirmation message is always the same**, whether or not an account exists for the address you typed. This protects your privacy by never revealing who is registered. If you have an account, the email will arrive.
- **The reset link expires after 60 minutes.** If you open it too late, just request another.

:::note
If you would rather skip the reset entirely, a @doc(auth.magicLinks, "magic link") can sign you in without a password. Once you are in, you can set a new password from your profile.
:::

## If you just want to change it

You do not need the forgot password flow to rotate your password. Go to your profile, open the security area, and change your password there. You will enter your current password and choose the new one.

## Why a password might be refused

KolleK checks every new password against two rules, so a rejection is never a mystery:

- **At least eight characters.** Shorter passwords are refused outright.
- **No known breached passwords.** Your candidate password is checked against lists of passwords that have appeared in public data breaches. If it has ever leaked anywhere, it is refused, even if it looks strong. This is about the password itself, not your account, so choose something you have not used on other sites.

A password manager sidesteps both rules effortlessly by generating something long and unique.

## Where to next

- Add a second step so a stolen password is not enough: @doc(security.twoFactorAuth).
- Still cannot get in? Work through @doc(troubleshooting.signIn).
- Reset email never arrived? See @doc(troubleshooting.emailDelivery).
