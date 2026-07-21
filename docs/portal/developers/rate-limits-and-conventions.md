# Rate limits and conventions

A handful of conventions apply across the whole API. Learning them once saves you surprises on every endpoint, so they live here rather than being repeated through the reference.

## Rate limits

- Authenticated requests are limited to **60 per minute** per user.
- `POST /api/register` and `POST /api/login` are limited to **6 per minute**, which protects against credential stuffing.

When you exceed a limit, the API answers with HTTP 429. Back off and retry after a moment. If you are writing a bulk import, pace your requests rather than firing them as fast as possible, and remember the API works on one object per request, as there are no bulk endpoints.

## Pagination

List endpoints are paginated and share one envelope:

- `data` holds the page of resources.
- `links` holds `first`, `last`, `prev`, and `next` URLs.
- `meta` holds the current page, the total count, and related details.

Pages hold **10 resources by default**. Ask for more with the `per_page` query parameter, up to a **maximum of 100**. Follow `links.next` until it is `null` to walk a whole list.

## Money is in the smallest currency unit

Every amount in the API (estimated values, transaction amounts, deposits, insured values) is an integer in the smallest unit of its currency. For dollars and euros that means cents: a purchase of $49.99 travels as `4999`. This avoids floating point rounding entirely. Convert for display in your own code, and remember each [collection](../core-concepts/collections.md) carries its own currency.

## Forbidden reads as not found

The API enforces the same [roles](../core-concepts/accounts-users-and-roles.md) as the web app, with one deliberate twist: an action you are not allowed to perform, or a resource in another account, answers **404 Not Found**, not 403 Forbidden. A caller cannot tell "this does not exist" apart from "this is not yours", so the API never confirms what exists outside your account.

:::note
If an endpoint unexpectedly returns 404 on an object you can see in the app, check the role of the user whose token you are using. A viewer's token gets 404 on every write.
:::

## Errors and validation

Failed validation answers HTTP 422 with a `message` and an `errors` object keyed by field name. Other errors follow ordinary HTTP semantics: 401 when the token is missing or revoked, 404 as described above, 429 for rate limits.

## Where to next

- See these conventions applied on real endpoints in the generated reference at `/docs/api`.
- Ready for event delivery someday? Read where [Webhooks](webhooks.md) stand today.
