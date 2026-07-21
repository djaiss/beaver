---
id: reference.glossary
title: Glossary
slug: glossary
section: reference
---

# Glossary

Every product term in one place. Each entry links to the page that explains the idea fully. Terms are listed in the order you meet them in the product, from the workspace down to the records on a single copy.

## The workspace

**Account.** Your private workspace, and the boundary around everything you create. Every collection, item, and setting lives inside exactly one account. See @doc(accounts.usersAndRoles).

**User.** A person who signs in. A user belongs to exactly one account and cannot join a second one with the same email. See @doc(accounts.usersAndRoles).

**Role.** What a user is allowed to do in their account: a viewer reads, an editor catalogues, an owner also manages the account. See @doc(collaboration.rolesInPractice, "Understanding the three roles in practice").

## The catalogue

**Collection.** A top level group you name, such as "My Comics" or "Wine Cellar". Collections hold items and carry their own currency and visibility. See @doc(collections.overview).

**Collection type.** A kind of thing you collect (Comics, Vinyl Records, Wine) that decides which custom fields its items record. Types are shared across the account. See @doc(collectionTypes.overview).

**Custom field.** A detail you define on a collection type, such as "Issue #" or "Vintage". Its value is recorded on each item. See @doc(collectionTypes.overview).

**Field group.** A named section, such as "Publishing info", that keeps a long list of custom fields readable on the item form. See @doc(collectionTypes.setup).

**Item.** The kind of thing you catalogue, such as "Amazing Spider-Man #1". Descriptive details live on the item; the physical things you own are its copies. See @doc(items.itemsVsCopies).

**Copy.** One physical instance of an item that you actually hold. Each copy has its own condition, location, value, and history. See @doc(items.itemsVsCopies).

## Grouping and finding

**Category.** A filing tool inside one collection. Categories can nest, such as Marvel inside Comics. See @doc(organizing.categoriesSetsAndSeries).

**Set.** A finite list you are trying to complete within one collection, tracked against a target count. See @doc(organizing.categoriesSetsAndSeries).

**Series.** A franchise that can span several collections, such as Harry Potter across books and films. A series does not track completion. See @doc(organizing.categoriesSetsAndSeries).

**Tag.** A free form label shared across every collection in the account, such as "Signed". An item can carry many tags. See @doc(tags.overview).

**Location.** Where a copy physically lives. Locations nest to model real spaces, such as a box on a shelf in a room. See @doc(locations.overview).

**Condition.** A grade describing the state of a copy, such as New or Damaged. See @doc(conditions.overview).

## A copy's history

**Transaction.** A money or ownership event on a copy, such as a purchase or sale. All money lives in transactions. See @doc(copies.recordPaymentsAndValue).

**Valuation.** What a copy was worth at a point in time. A copy's current estimated value is its most recent valuation. See @doc(copies.recordPaymentsAndValue).

**Insurance record.** Coverage recorded for a copy: provider, insured value, policy details, and status. See @doc(copies.insure).

**Loan.** A custody record for a copy you lent out or borrowed in, with its dates, party, and return details. See @doc(loans.lendAndBorrow).

**Maintenance record.** Care or repair work performed on a copy, such as cleaning or restoration. See @doc(copies.recordMaintenance).

**Provenance event.** One chapter in the ownership and authenticity story of a copy, such as an acquisition, exhibition, or appraisal. See @doc(copies.traceProvenance).

**Location history.** The dated record of where a copy has lived over time. Moving a copy closes one record and opens the next. See @doc(copies.move).

**Document.** A file or external link kept with a copy or one of its records, such as a receipt on a transaction. See @doc(copies.attachDocuments).

## Access and safety

**Visibility.** A collection setting recording who it is meant for: private (you alone), shared (everyone in the account), or public (anyone with the link, read only). Recorded today, enforced once sharing arrives. See @doc(sharing.overview).

**Trash.** Where deleted collections, items, copies, categories, and sets wait before being purged, and from where they can be restored. See @doc(dataSafety.restoreFromTrash).

**Instance administrator.** A server wide flag, separate from account roles, that unlocks the administration panel for whoever runs the instance. See @doc(instanceAdmin.grantAccess).

**API key.** A personal token that lets a script or application call the KolleK API as you. See @doc(apiKeys.manage).

**Webhook.** A URL you register to receive signed notifications from KolleK. No application event fires one yet. See @doc(webhooks.overview).

## Where to next

- Every option these terms can take: @doc(reference.fieldAndStatus).
- The concepts behind the terms, explained properly: @doc(coreConcepts.index).
