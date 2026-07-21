---
id: accounts.delete
title: Delete an account
slug: delete-an-account
section: data-safety
---

# Delete an account

Deleting an account is the most destructive action in KolleK. It removes the entire workspace: every collection, every item, every copy with its full history, every photo and document, and every member's access. Only an @doc(accounts.usersAndRoles, "owner") can do it.

:::warning
Deleting an account cannot be undone. Nothing goes to the trash, nothing can be restored, and no one, including whoever runs the instance, can bring it back. Every member loses everything at once.
:::

## Before you delete

Slow down and check three things:

- **Is this really what you want, rather than @doc(users.deleteSelf, "deleting your own user")?** Leaving a shared account only requires removing yourself. The account and the catalogue survive without you.
- **Does anyone else depend on it?** Every member of the account loses access and data the moment you confirm. Tell them first.
- **Do you have what you need out of it?** Export any @doc(collectionTypes.importExport, "collection type definitions") you want to keep. If the instance is self hosted, take a full backup first, as described in @doc(selfHosting.backupAndRestore). After deletion there is nothing left to back up.

## Delete the account

From **Account settings**, find the deletion option in the danger zone, and confirm. The account and everything in it are removed, and all members are signed out for good.

## What is gone afterwards

Everything. Collections, items, copies, categories, sets, series, tags, locations, custom types and fields, photos, documents, the full copy histories, the activity trail, all members, and any pending invitations. The email addresses involved become free to register fresh accounts, but those accounts start empty.

## Where to next

- Removing just yourself is covered in @doc(users.deleteSelf).
- For recoverable deletions, see @doc(dataSafety.restoreFromTrash).
