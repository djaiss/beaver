---
id: items.addAndEdit
title: Add and edit items
slug: add-and-edit-items
section: core-features
---

# Add and edit items

This is the page for the thing you will do most: putting entries into your catalogue. It walks through the item form field by field, explains which parts are optional (almost all of them), and covers editing and deleting.

If the difference between an item and a copy is still fuzzy, read @doc(items.itemsVsCopies) first. In short: the item describes the kind of thing, the copies record what you physically own.

## Who can do this

Adding and editing items requires the **editor** or **owner** @doc(accounts.usersAndRoles, "role").

## Add an item

::::steps
:::step title="Open the collection"
Open the collection the item belongs to and choose **New item**.

::screenshot{label="Collection view, New item button"}
:::

:::step title="Name it"
Enter the **name**. This is the only required field. Emma types "Amazing Spider-Man #300". Everything else can be added now or later.
:::

:::step title="Classify it"
Optionally pick a **type**, a **category**, a **set**, and a **series**, and add **tags**. The type is the important one: choosing it makes that type's custom fields appear on the form.

::screenshot{label="Item form, type and classification fields"}
:::

:::step title="Fill in the details"
Fill the **custom fields** the type provides, upload **photos**, and record the **copies** you own, all on the same form.
:::

:::step title="Save"
Save the item. It appears in the collection immediately.
:::
::::

## The form, field by field

- **Name.** Required, and the only thing that is.
- **Description.** Free text for anything that does not fit elsewhere.
- **Type.** Which @doc(collectionTypes.overview, "collection type") this item is. Only types enabled on the collection are offered. The type decides which custom fields appear below.
- **Category.** Where the item files within this collection. See @doc(categories.organizeItems).
- **Set.** A finite list you are completing. See @doc(sets.trackCompletion).
- **Series.** A franchise that can span collections. See @doc(series.groupFranchise).
- **Tags.** Pick existing @doc(tags.overview, "tags") or type a new one and it is created on the spot.
- **Custom fields.** Whatever the chosen type defines: text, numbers, dates, yes or no switches, select lists, and ratings up to five stars. Fields appear grouped the way the type organizes them.
- **Photos.** Covered fully in @doc(items.addPhotos).
- **Copies.** One or more physical copies, added inline. Covered fully in @doc(copies.track).

Do not feel obliged to fill everything in one sitting. A name now and details later is a perfectly good workflow, and the same form serves both.

## Edit an item

Open the item and choose to edit it. It is the same form, pre filled. Change what you need and save.

## Delete an item

Open the item, choose to delete it, and confirm.

:::warning
Deleting an item sends it and its copies to the trash. It is removed permanently after the retention period (30 days by default).
:::

Until then you can bring it back. See @doc(dataSafety.restoreFromTrash).

## Where to next

- Record what you physically own: @doc(copies.track).
- Make the catalogue visual: @doc(items.addPhotos).
- Start recording money and history: @doc(copyHistory.concept, "A copy's history explained").
