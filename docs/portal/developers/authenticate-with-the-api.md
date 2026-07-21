# Authenticate with the API

Every API request is authenticated with a bearer token. This page walks you from zero to your first successful request, then covers getting tokens through the API itself and revoking them.

Replace `https://kollek.example.com` in the examples with the address of your instance. The API lives under `/api` on that address.

## The quickest path: create a key in the app

The easiest way to get a token is to create an API key from your profile.

::::steps
:::step title="Create an API key"
In the app, open your profile settings and go to **API keys**. Create a key and give it a label you will recognize later, such as "Reporting script".

::screenshot{label="Profile settings, API keys page with the new key form"}
:::

:::step title="Copy the token"
The token is displayed once, right after creation. Copy it now and store it somewhere safe, such as a password manager. If you lose it, revoke the key and create a new one.
:::

:::step title="Make your first request"
Send the token in the `Authorization` header. A good first call is `/api/me`, which returns your own user:

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

If you get back a JSON document describing your user, you are authenticated. Creating and revoking keys, and seeing when each was last used, is covered in [Manage API keys](../security/manage-api-keys.md).

:::note
Tokens do not expire on their own. They work until you revoke them, so treat a token like a password.
:::

## Getting a token through the API

You can also authenticate entirely over HTTP, which suits scripts and integrations that manage their own credentials.

Log in with your email and password to receive a token:

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

The response contains your token under `data.token`. The optional `device_name` names the token so you can recognize it later in your key list.

Two things to know:

- If [two factor authentication](../security/two-factor-authentication.md) is enabled on your user, the login endpoint also requires a `code` field containing a current TOTP code from your authenticator app, or one of your [recovery codes](../security/recovery-codes.md).
- Registering through the API works too: `POST /api/register` creates a user with its own account and returns a token, exactly like signing up in the browser.

Both endpoints are limited to 6 requests per minute, which is plenty for real sign ins and stops brute force attempts.

## Revoking tokens

You have two options:

- `DELETE /api/logout` revokes the token that made the request. Use this when a script finishes with a temporary token.
- The **API keys** page in your profile lists every token and can revoke any of them. The API keys endpoints in the generated reference do the same over HTTP.

KolleK emails you when a key is created or deleted from the app, so unexpected key activity does not go unnoticed. See [Login and security alert emails](../security/security-alert-emails.md).

## Where to next

- Learn the request conventions in [Rate limits and conventions](rate-limits-and-conventions.md).
- Manage your tokens in [Manage API keys](../security/manage-api-keys.md).
- Explore every endpoint in the generated reference at `/docs/api`.
