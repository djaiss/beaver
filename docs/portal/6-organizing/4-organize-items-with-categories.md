---
id: categories.organizeItems
title: Organize items with categories
slug: organize-items-with-categories
section: organizing
---

# Organize items with categories

A @doc(organizing.categoriesSetsAndSeries, "category") files items inside one collection, and categories can nest to any depth. They answer the question "where does this belong" once a collection grows past the point of scrolling.

You need the editor or owner role to create or change categories. Everyone in the account can browse them.

## When categories help

Noah's "Vinyl" collection passed three hundred records, and scrolling stopped working. He creates categories for "Rock" and "Jazz", then nests "Bebop" and "Fusion" under "Jazz". Each record is filed in exactly one place, and each category page shows only its own slice of the collection.

Categories suit one collection's internal structure. If you want a label that cuts across collections instead, use @doc(tags.overview, "tags"). If you are tracking a finite list to complete, use a @doc(sets.trackCompletion, "set").

## Create and nest categories

::::steps
:::step title="Open the collection's categories"
Open the collection and go to its **Categories**.
:::

:::step title="Create a category"
Choose **New category** and give it a name. To nest it, pick a parent category. Noah creates "Jazz" first, then "Bebop" with "Jazz" as its parent.

::screenshot{label="New category form with the parent picker"}
:::

:::step title="File items into it"
When adding or editing an item, choose the category on the item form. An item belongs to at most one category.
:::
::::

## Browse a category

Opening a category shows the collection filtered to just that category, with its own item count and a statistics panel for that slice. It is the fastest way to answer "how much jazz do I actually have".

## Rename, move, and delete

You can rename a category or move it under a different parent at any time. Items stay filed where they are.

Deleting is a soft delete: the category goes to the @doc(dataSafety.restoreFromTrash, "trash") and can be restored for a while.

:::warning
Deleting a category also deletes every category nested underneath it. The items themselves are never deleted, they simply become uncategorized, but the whole branch of the filing tree goes to the trash together.
:::

## Where to next

- Compare the three grouping tools in @doc(organizing.categoriesSetsAndSeries).
- Track what you still need with @doc(sets.trackCompletion).
- Recover a deleted category in @doc(dataSafety.restoreFromTrash).
