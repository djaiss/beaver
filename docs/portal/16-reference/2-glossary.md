---
id: reference.glossary
title: Glossary
slug: glossary
section: reference
---

# Glossary

Every product term in one place. Each entry links to the page that explains the idea fully. Terms are listed in the order you meet them in the product, from the workspace down to the records on a single copy.

## The workspace

**Account.** Your private workspace, and the boundary around everything you create. Every collection, item, and setting lives inside exactly one account. See [Accounts, users, and roles](../3-core-concepts/3-accounts-users-and-roles.md).

**User.** A person who signs in. A user belongs to exactly one account and cannot join a second one with the same email. See [Accounts, users, and roles](../3-core-concepts/3-accounts-users-and-roles.md).

**Role.** What a user is allowed to do in their account: a viewer reads, an editor catalogues, an owner also manages the account. See [Understanding the three roles in practice](../8-collaboration/4-roles-in-practice.md).

## The catalogue

**Collection.** A top level group you name, such as "My Comics" or "Wine Cellar". Collections hold items and carry their own currency and visibility. See [Collections](../3-core-concepts/4-collections.md).

**Collection type.** A kind of thing you collect (Comics, Vinyl Records, Wine) that decides which custom fields its items record. Types are shared across the account. See [Collection types and custom fields](../3-core-concepts/6-collection-types-and-custom-fields.md).

**Custom field.** A detail you define on a collection type, such as "Issue #" or "Vintage". Its value is recorded on each item. See [Collection types and custom fields](../3-core-concepts/6-collection-types-and-custom-fields.md).

**Field group.** A named section, such as "Publishing info", that keeps a long list of custom fields readable on the item form. See [Set up collection types and custom fields](../6-organizing/2-set-up-collection-types-and-custom-fields.md).

**Item.** The kind of thing you catalogue, such as "Amazing Spider-Man #1". Descriptive details live on the item; the physical things you own are its copies. See [Items versus copies](../3-core-concepts/5-items-and-copies.md).

**Copy.** One physical instance of an item that you actually hold. Each copy has its own condition, location, value, and history. See [Items versus copies](../3-core-concepts/5-items-and-copies.md).

## Grouping and finding

**Category.** A filing tool inside one collection. Categories can nest, such as Marvel inside Comics. See [Categories, sets, and series](../3-core-concepts/7-categories-sets-and-series.md).

**Set.** A finite list you are trying to complete within one collection, tracked against a target count. See [Categories, sets, and series](../3-core-concepts/7-categories-sets-and-series.md).

**Series.** A franchise that can span several collections, such as Harry Potter across books and films. A series does not track completion. See [Categories, sets, and series](../3-core-concepts/7-categories-sets-and-series.md).

**Tag.** A free form label shared across every collection in the account, such as "Signed". An item can carry many tags. See [Tags](../3-core-concepts/8-tags.md).

**Location.** Where a copy physically lives. Locations nest to model real spaces, such as a box on a shelf in a room. See [Locations](../3-core-concepts/9-locations.md).

**Condition.** A grade describing the state of a copy, such as New or Damaged. See [Conditions](../3-core-concepts/10-conditions.md).

## A copy's history

**Transaction.** A money or ownership event on a copy, such as a purchase or sale. All money lives in transactions. See [Record what you paid and what it is worth](../5-copy-history/2-record-payments-and-value.md).

**Valuation.** What a copy was worth at a point in time. A copy's current estimated value is its most recent valuation. See [Record what you paid and what it is worth](../5-copy-history/2-record-payments-and-value.md).

**Insurance record.** Coverage recorded for a copy: provider, insured value, policy details, and status. See [Insure a copy](../5-copy-history/3-insure-a-copy.md).

**Loan.** A custody record for a copy you lent out or borrowed in, with its dates, party, and return details. See [Lend and borrow copies](../5-copy-history/4-lend-and-borrow-copies.md).

**Maintenance record.** Care or repair work performed on a copy, such as cleaning or restoration. See [Record maintenance and repairs](../5-copy-history/5-record-maintenance-and-repairs.md).

**Provenance event.** One chapter in the ownership and authenticity story of a copy, such as an acquisition, exhibition, or appraisal. See [Trace a copy's provenance](../5-copy-history/6-trace-provenance.md).

**Location history.** The dated record of where a copy has lived over time. Moving a copy closes one record and opens the next. See [Move a copy and keep its location history](../5-copy-history/7-move-a-copy.md).

**Document.** A file or external link kept with a copy or one of its records, such as a receipt on a transaction. See [Attach documents to a copy](../5-copy-history/8-attach-documents.md).

## Access and safety

**Visibility.** A collection setting recording who it is meant for: private (you alone), shared (everyone in the account), or public (anyone with the link, read only). Recorded today, enforced once sharing arrives. See [Visibility and sharing](../3-core-concepts/12-visibility-and-sharing.md).

**Trash.** Where deleted collections, items, copies, categories, and sets wait before being purged, and from where they can be restored. See [Restore something from the trash](../11-data-safety/2-restore-from-trash.md).

**Instance administrator.** A server wide flag, separate from account roles, that unlocks the administration panel for whoever runs the instance. See [Grant instance administrator access](../14-self-hosting/9-grant-instance-administrator-access.md).

**API key.** A personal token that lets a script or application call the KolleK API as you. See [Manage API keys](../10-security/7-manage-api-keys.md).

**Webhook.** A URL you register to receive signed notifications from KolleK. No application event fires one yet. See [Webhooks](../13-developers/5-webhooks.md).

## Where to next

- Every option these terms can take: [Field and status reference](3-field-and-status-reference.md).
- The concepts behind the terms, explained properly: [Core Concepts](../3-core-concepts/1-introduction.md).
