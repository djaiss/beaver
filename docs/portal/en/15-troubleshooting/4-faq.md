---
id: troubleshooting.faq
title: Frequently asked questions
slug: faq
section: troubleshooting
---

# Frequently asked questions

Short answers to the questions that come up again and again. Each links to the page that covers the topic properly.

## What is the difference between an item and a copy?

An item is the kind of thing, such as "Amazing Spider-Man #1". A copy is one physical instance you actually own. Own three of the same comic, and that is one item with three copies, each carrying its own condition, location, value, and history. This is the single most important idea in KolleK. See @doc(items.itemsVsCopies).

## Can I belong to more than one account?

No. One user belongs to exactly one account, and an email address can only have one user. This also means an invitation to someone else's account cannot be accepted by an email that already has its own account. See @doc(accounts.usersAndRoles).

## Is KolleK really free?

Yes. There is no billing inside the app at all: no plans, no tiers, no paywalled features. Self hosting is free, and every feature is included however you run it. See @doc(kollek.hostingOptions).

## How do I get my data out?

Today, from inside the app, you can export @doc(collectionTypes.importExport, "collection type definitions as JSON"). There is no item or whole collection export yet. The complete answer for self hosters is an instance level backup of the database and uploaded files, covered in @doc(selfHosting.backupAndRestore). The honest summary lives in @doc(dataSafety.backupCollectionData).

## Why can I not remove or demote the last owner?

An account must always keep at least one owner, otherwise nobody could manage it, invite members, or delete it. Promote someone else to owner first. See @doc(collaboration.manageMembersAndRoles).

## Where is the search feature?

Searching across everything from the dashboard is not available yet; the box you see there is a placeholder. What works today: filtering within a collection you have open, and searching your photo library. See @doc(troubleshooting.featureStatus).

## Do webhooks work yet?

Half of them. You can register endpoints and each gets a signing secret, but no application event fires a webhook yet. The delivery machinery is ready; the events arrive as the product grows. See @doc(webhooks.overview).

## Is my data encrypted, and what does that protect?

Sensitive fields are encrypted at rest in the database with your instance's key. That protects the database contents if the database alone is stolen. It is not end to end encryption: whoever runs the instance holds the key and can access the data. See @doc(dataSafety.howProtected).

## Can I add my own conditions?

Yes. Open **Item conditions** in account settings to add, rename, or delete conditions, including the seeded ones (New, Like New, Used, Worn, Damaged). See @doc(conditions.manage).

## Something was deleted. Can I get it back?

If it was a collection, item, copy, category, or set, it went to the trash and can be restored for 30 days by default. Photos, documents, and history records are removed immediately and cannot be recovered from inside the app. See @doc(dataSafety.restoreFromTrash).

## Still stuck?

- Sign in problems: @doc(troubleshooting.signIn).
- Missing emails: @doc(troubleshooting.emailDelivery).
- What is finished and what is not: @doc(troubleshooting.featureStatus).
