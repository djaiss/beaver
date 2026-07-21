---
id: webhooks.overview
title: Webhooks
slug: webhooks
section: developers
---

# Webhooks

Webhooks let an external system receive an HTTP call from KolleK when something happens in your account. You can set them up today, and this page shows how. But read the next paragraph first, because it frames everything else.

:::note
No application event currently triggers a webhook. The registration, signing, and delivery machinery is in place and tested, but events will only start firing as the collection domain grows. Set up your receiver now if you like; just do not wait on it for anything yet. The [feature status page](../15-troubleshooting/5-feature-status.md) tracks when this changes.
:::

## What exists today

Registering an endpoint stores a destination URL with its own signing secret. When KolleK eventually fires events, each one will be delivered to every active endpoint you have registered, signed so your receiver can verify it really came from your instance.

Webhook endpoints belong to your user, not to the whole account.

## Register an endpoint

From the app, open your profile settings and go to **Webhooks**. Add the URL your receiver listens on, with a label so you remember what it is for. Each endpoint receives its own signing secret, a 64 character string generated when the endpoint is created. Store it with your receiver.

An operator can also create an endpoint from the command line:

```bash
php artisan beaver:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

The command prints the endpoint id and its signing secret.

## The payload your receiver should expect

Every delivery is a JSON `POST` with this shape:

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` names what happened. No event names are defined yet.
- `happened_at` is an ISO 8601 timestamp of when it happened.
- `data` carries the payload for that event.

## Verifying signatures

Each delivery includes a `Signature` header: an HMAC SHA256 hash of the raw request body, computed with your endpoint's signing secret. Recompute the same hash on your side and compare. If they differ, discard the request, because it did not come from your instance.

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## Delivery and retries

Deliveries are queued and sent in the background. A delivery that fails is retried up to 3 times with exponential backoff. Your receiver should respond quickly with a 2xx status and do its real work asynchronously.

On a self hosted instance, deliveries run on the queue worker, so the queue role must be running. See [Install with Docker](../14-self-hosting/2-install-with-docker.md).

## Where to next

- Check what is live and what is pending on the [feature status page](../15-troubleshooting/5-feature-status.md).
- Build against the API in the meantime, starting with [Authenticate with the API](3-authenticate-with-the-api.md).
