# Manage API keys

An API key is a personal token that lets a script or application act as you through the KolleK API. This page covers the lifecycle: creating a key, keeping track of it, and revoking it. What you can actually do with a key lives in the [developers section](../developers/authenticate-with-the-api.md).

If you never plan to use the API, you can skip this page entirely. No keys exist until you create one.

## Create a key

::::steps
:::step title="Open your API key settings"
Go to your profile and open the API keys area. You will see any keys you already have, each with the date it was last used.
:::

:::step title="Name the new key"
Choose to create a key and give it a **label** that says what it is for, such as "Import script" or "Home dashboard". Labels are for future you, deciding which key is safe to revoke.
:::

:::step title="Copy the token immediately"
KolleK shows the token once, right after creation. Copy it now and store it somewhere safe, such as a password manager.

::screenshot{label="New API key with the token revealed once"}
:::
::::

:::warning
The token is shown only once. If you lose it, you cannot view it again. Revoke the key and create a new one.
:::

KolleK emails you a notice whenever a key is created on your user, so an unexpected key never goes unnoticed.

## Keep track of your keys

The API keys area lists every key with its label and when it was last used. That last used time is your friend: a key that has not been used in months is a key you can probably revoke, and a key used five minutes ago when your script has not run is a key to investigate.

One habit keeps this manageable: one key per purpose. When each integration has its own key, you can revoke one without breaking the others.

## Revoke a key

Delete the key from the same list. Anything still using its token stops working immediately, and KolleK emails you a notice of the deletion.

Revoke a key when:

- You no longer use the script or app it belonged to.
- The token may have leaked, for example it was committed to a repository or shared in a chat.
- You received a [key created or deleted alert](security-alert-emails.md) you do not recognize. In that case, change your password too.

:::note
Signing in through the API also creates a token behind the scenes. Those sign in tokens do not trigger the key created email, so the alerts you receive stay meaningful.
:::

## Where to next

- Put a key to use with your first request: [Authenticate with the API](../developers/authenticate-with-the-api.md).
- Understand the emails around keys: [Login and security alert emails](security-alert-emails.md).
