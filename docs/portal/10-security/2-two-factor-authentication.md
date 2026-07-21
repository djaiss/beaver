---
id: security.twoFactorAuth
title: Protect your account with two factor authentication
slug: two-factor-authentication
section: security
---

# Protect your account with two factor authentication

Two factor authentication adds a second step to signing in. After your password is accepted, KolleK asks for a six digit code from an authenticator app on your phone. Even if someone learns your password, they cannot get in without that code.

This is the most effective security control KolleK offers, and it takes a few minutes to set up.

## What you will need

An authenticator app on your phone, such as any app that supports time based one time codes. If you have ever scanned a QR code to protect another account, you already have one.

## Turn it on

::::steps
:::step title="Open your security settings"
Go to your profile and open the security area, then choose to set up **two factor authentication**.
:::

:::step title="Scan the QR code"
KolleK shows a QR code. Open your authenticator app, add a new account, and scan the code. The app starts showing a six digit code for KolleK that changes every 30 seconds.

::screenshot{label="Two factor setup screen with the QR code"}
:::

:::step title="Confirm with a code"
Type the current six digit code from your app into the confirmation field and submit. This proves the app and KolleK are in sync before anything changes about how you sign in.
:::

:::step title="Save your recovery codes"
KolleK generates eight recovery codes. Copy them somewhere safe that is not your phone, such as a password manager or a printed page. Each code can sign you in once if you ever lose your authenticator.

::screenshot{label="The eight recovery codes shown after setup"}
:::
::::

:::warning
If you lose your authenticator and have no recovery codes, you cannot complete the two factor step, and you may be locked out of your user. Save the codes before you close the page.
:::

## What changes when you sign in

From now on, signing in with your email and password takes one extra step. After your password is accepted, KolleK asks for the current code from your authenticator app. Enter it and you are in.

If you cannot reach your app, enter one of your @doc(security.recoveryCodes, "recovery codes") instead.

:::note
Signing in with a @doc(auth.magicLinks, "magic link") does not ask for a two factor code. Access to your email inbox already acts as the second factor, so protect that inbox accordingly.
:::

## Turn it off

You can disable two factor authentication from the same security area. Doing so removes the code step from sign in and also deletes your recovery codes and the pairing with your authenticator app. If you turn it back on later, you will scan a new QR code and receive a fresh set of recovery codes.

## Where to next

- Make sure your fallback works: @doc(security.recoveryCodes).
- Understand the passwordless path and its trade off: @doc(auth.magicLinks).
- See every way to get into the app: @doc(auth.signIn).
