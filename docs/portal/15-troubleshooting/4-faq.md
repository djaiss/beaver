---
id: troubleshooting.faq
title: Frequently asked questions
slug: faq
section: troubleshooting
---

# Frequently asked questions

Short answers to the questions that come up again and again. Each links to the page that covers the topic properly.

## What is the difference between an item and a copy?

An item is the kind of thing, such as "Amazing Spider-Man #1". A copy is one physical instance you actually own. Own three of the same comic, and that is one item with three copies, each carrying its own condition, location, value, and history. This is the single most important idea in KolleK. See [Items versus copies](../3-core-concepts/5-items-and-copies.md).

## Can I belong to more than one account?

No. One user belongs to exactly one account, and an email address can only have one user. This also means an invitation to someone else's account cannot be accepted by an email that already has its own account. See [Accounts, users, and roles](../3-core-concepts/3-accounts-users-and-roles.md).

## Is KolleK really free?

Yes. There is no billing inside the app at all: no plans, no tiers, no paywalled features. Self hosting is free, and every feature is included however you run it. See [Cloud version versus self hosting](../2-getting-started/3-cloud-vs-self-hosting.md).

## How do I get my data out?

Today, from inside the app, you can export [collection type definitions as JSON](../6-organizing/3-import-and-export-a-collection-type.md). There is no item or whole collection export yet. The complete answer for self hosters is an instance level backup of the database and uploaded files, covered in [Back up and restore your instance](../14-self-hosting/7-back-up-and-restore.md). The honest summary lives in [Back up your collection data](../11-data-safety/6-back-up-your-collection-data.md).

## Why can I not remove or demote the last owner?

An account must always keep at least one owner, otherwise nobody could manage it, invite members, or delete it. Promote someone else to owner first. See [Manage members and roles](../8-collaboration/3-manage-members-and-roles.md).

## Where is the search feature?

Searching across everything from the dashboard is not available yet; the box you see there is a placeholder. What works today: filtering within a collection you have open, and searching your photo library. See [Feature status and roadmap](5-feature-status.md).

## Do webhooks work yet?

Half of them. You can register endpoints and each gets a signing secret, but no application event fires a webhook yet. The delivery machinery is ready; the events arrive as the product grows. See [Webhooks](../13-developers/5-webhooks.md).

## Is my data encrypted, and what does that protect?

Sensitive fields are encrypted at rest in the database with your instance's key. That protects the database contents if the database alone is stolen. It is not end to end encryption: whoever runs the instance holds the key and can access the data. See [How your data is protected](../3-core-concepts/13-how-your-data-is-protected.md).

## Can I add my own conditions?

Not from the web app yet. The seeded conditions (New, Like New, Used, Worn, Damaged) appear as dropdowns everywhere, and adding or renaming conditions is currently only possible through the API. See [Conditions](../3-core-concepts/10-conditions.md) and [Feature status and roadmap](5-feature-status.md).

## Something was deleted. Can I get it back?

If it was a collection, item, copy, category, or set, it went to the trash and can be restored for 30 days by default. Photos, documents, and history records are removed immediately and cannot be recovered from inside the app. See [Restore something from the trash](../11-data-safety/2-restore-from-trash.md).

## Still stuck?

- Sign in problems: [Troubleshooting sign in](2-troubleshoot-sign-in.md).
- Missing emails: [Troubleshooting email delivery](3-troubleshoot-email-delivery.md).
- What is finished and what is not: [Feature status and roadmap](5-feature-status.md).
