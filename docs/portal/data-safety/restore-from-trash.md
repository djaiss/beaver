# Restore something from the trash

Most everyday deletions in KolleK are not final. Collections, items, copies, categories, and sets go to the trash first, where they wait before being removed for good. This page explains what lands there, how long it stays, and how to bring something back.

You need the editor or owner role to restore or permanently delete.

## What goes to the trash, and what does not

Five kinds of objects soft delete to the trash:

- [Collections](../core-features/create-and-manage-collections.md), together with what they contain
- [Items](../core-features/add-and-edit-items.md)
- [Copies](../core-features/track-the-copies-you-own.md)
- [Categories](../organizing/organize-items-with-categories.md)
- [Sets](../organizing/track-a-set-to-completion.md)

:::note
Photos, documents, and the history records on a copy (transactions, valuations, loans, and the rest) do not go to the trash. Deleting one of those removes it immediately and permanently.
:::

## How long things are kept

Trashed objects are kept for a retention period, 30 days unless whoever runs your instance configured a different one. A daily cleanup permanently removes anything past its time. Each entry in the trash shows how many days it has left, and the list is sorted with the most urgent first, so what is about to disappear is at the top.

## Restore something

::::steps
:::step title="Open the trash"
Go to the **Trash** from your account. You can search it if the list is long.

::screenshot{label="Trash list with days left per entry"}
:::

:::step title="Find the entry"
Each entry shows what it is, when it was deleted, and who deleted it.
:::

:::step title="Restore it"
Choose **Restore**. The object returns exactly where it was, with its data intact.
:::
::::

If you deleted a collection by mistake, restoring it also brings back what it held. Restore parents before hunting for their children.

## Empty the trash

You can also permanently delete everything in the trash at once, without waiting out the retention period.

:::warning
Emptying the trash is permanent. Everything in it is removed for good, and nothing can be recovered afterwards.
:::

## Where to next

- Deleting yourself rather than your data? See [Delete your user](delete-your-user.md).
- Self hosting and want real safety nets? See [Back up and restore your instance](../self-hosting/back-up-and-restore.md).
