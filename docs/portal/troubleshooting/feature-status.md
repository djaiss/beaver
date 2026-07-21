# Feature status and roadmap

KolleK is growing, and a few capabilities are visible before they are finished. This page is the single honest list of what is fully available today and what is still on its way, so no other page has to hedge. When the product moves, this page moves with it.

## Available now

Everything else documented in this portal works as described, including:

- Collections, items, copies, photos, tags, categories, sets, and series.
- Collection types with custom fields, including import and export of type definitions as JSON.
- The full copy history: transactions, valuations, insurance, loans, maintenance, provenance, location history, and documents, with the unified timeline.
- Collaboration with owner, editor, and viewer roles, and email invitations.
- Two factor authentication, magic links, API keys, and security alert emails.
- The complete JSON API with its generated reference at `/docs/api`.
- Self hosting with Docker, encrypted data at rest, trash with restore, and per collection statistics.

## Not yet

### Global search

The search box on the dashboard is a placeholder and does not search anything yet. What works today: filtering the items of a collection you have open (see [Choose how to view a collection](../core-features/choose-how-to-view-a-collection.md)), and searching the [photo library](../organizing/photo-library.md).

### Collection visibility and sharing

Every collection carries a visibility setting (private, shared, or public), and the setting is saved, but it is not enforced yet. Every member of an account can still browse every collection in it, and there is no public link, so a collection marked public is not reachable from outside the account at all. Set visibility now to record your intent; it takes effect when sharing arrives. See [Visibility and sharing](../core-concepts/visibility-and-sharing.md).

### Webhook delivery

You can register webhook endpoints, and each receives a signing secret, but no application event fires a webhook yet. The signing and delivery machinery is in place, waiting for events to be wired up. Set up now if you like; deliveries arrive as the domain grows. See [Webhooks](../developers/webhooks.md).

### Managing conditions on the web

Conditions appear as ready made dropdown choices everywhere they are needed, but there is no web screen for creating or renaming them. That is currently only possible through the API. See [Conditions](../core-concepts/conditions.md).

### Item and collection import and export

Import and export exist for collection type definitions only. There is no item level or whole collection import or export yet. For getting everything out, self hosters have full instance backups; see [Back up your collection data](../data-safety/back-up-your-collection-data.md).

### Instance administration: Support and Reviews

In the instance administration panel, the Support and Reviews areas are placeholders that say as much. The rest of the panel works; see [The instance administration panel](../self-hosting/instance-administration-panel.md).

## How to read this page

Nothing here is a promise with a date. "Not yet" means the groundwork may exist, but you should not plan around the capability until it moves to the list above. When in doubt, trust this page over anything that seems to imply otherwise.

Questions this page does not answer are probably in the [FAQ](faq.md).
