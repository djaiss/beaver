# Signing in

KolleK gives you a few ways to sign in. This page covers each one so you can pick what suits you, and points you to the right place if you are locked out.

## Sign in with email and password

The usual way. Go to the sign in page, enter the **email** and **password** you registered with, and submit. You land on your dashboard.

If [two factor authentication](../security/two-factor-authentication.md) is turned on for your account, you will be asked for a code straight after your password. See below.

## Sign in with a magic link

If you would rather not type a password, KolleK can email you a link that signs you in.

On the sign in page, choose the magic link option, enter your **email**, and submit. KolleK sends a one time link to that address. Open it, and you are signed in.

Two things to know:

- **The link is valid for five minutes.** If it expires, just request another.
- **The link goes to your account email**, so you need access to that inbox. This is also what keeps it safe: only someone who can read your email can use it.

## The two factor step

If you have turned on two factor authentication, signing in with your password takes one extra step. After your password is accepted, KolleK asks for the current code from your authenticator app. Enter it to finish signing in.

If you cannot reach your authenticator, you can enter one of your [recovery codes](../security/recovery-codes.md) instead. Each recovery code works once.

:::warning
Signing in with a magic link does not ask for a two factor code, because access to your email inbox already acts as a second factor. If you rely on two factor authentication, keep this in mind when you choose how to sign in, and protect your email account accordingly.
:::

Setting up two factor authentication and saving recovery codes are covered in the **Security** section of this documentation.

## Forgot your password

If you cannot remember your password, use the "forgot password" link on the sign in page. Enter your email, and KolleK sends a reset link.

For your privacy, KolleK always shows the same confirmation message whether or not an account exists for that address, so the page will not reveal who is registered. If you have an account, the reset email will arrive. If you use a magic link to get back in, you can reset your password afterwards from your profile.

## Where to next

- New here and still setting up? Return to [The getting started checklist](getting-started-checklist.md).
- Want stronger protection? Turn on two factor authentication from the **Security** section.
