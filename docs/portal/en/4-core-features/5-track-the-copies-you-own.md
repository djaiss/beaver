---
id: copies.track
title: Track the copies you own
slug: track-the-copies-you-own
section: core-features
---

# Track the copies you own

An item on its own is just a description. A **copy** is your record of one physical instance you actually own, with its own condition, location, status, and history. This page covers adding copies and every field on a copy.

The idea behind this split is explained in @doc(items.itemsVsCopies). Adding copies requires the **editor** or **owner** role.

## Add a copy

Copies are added on the item form, inline, so you can record them while cataloguing.

::::steps
:::step title="Open the item"
Open the item and choose to edit it, then add a **copy**.
:::

:::step title="Record its physical state"
Pick its **condition** from the list and choose the **location** where it is stored.

::screenshot{label="Copy row, condition and location fields"}
:::

:::step title="Set its status and details"
Leave the **status** as Owned for something you have, or pick another status. Fill in any of the other fields that apply, then save the item.
:::
::::

Own two of the same thing? Add a second copy to the same item, never a second item. Each copy keeps its own condition, location, and history.

## The copy fields

- **Identifier.** A serial number, slab number, or any mark that pins down this exact copy. Priya records the serial number engraved on each of her watches.
- **@doc(conditions.overview, "Condition").** The grade of this copy, chosen from the ready made list (New, Like New, Used, Worn, Damaged, plus any your account has added).
- **@doc(locations.overview, "Location").** Where the copy currently lives. Changing it later through a move keeps the history; see @doc(copies.move, "Move a copy").
- **Status.** Where the copy stands in its life. See the list below.
- **Quantity.** For identical, interchangeable copies you do not need to tell apart, such as ten of the same unopened booster pack. If each copy matters individually, give each its own row instead.
- **Disposed date.** When the copy left your hands, for statuses like Sold or Disposed.
- **Note.** Anything worth remembering about this copy specifically.
- **Estimated value.** A quick figure for what the copy is worth. Behind the scenes it is saved as an "Own estimate" @doc(copies.recordPaymentsAndValue, "valuation"), opening the copy's value history rather than sitting on the copy itself. For anything you care about, add proper dated valuations there.

## The status lifecycle

- **Owned.** In your possession. The default.
- **Ordered.** Bought but not yet arrived.
- **Loaned out.** With someone else, but still yours. Custody moved, not ownership, so the copy still counts as held. Loans are best recorded through @doc(loans.lendAndBorrow), which sets this for you.
- **Sold, Gifted.** Ownership went to someone else.
- **Lost, Stolen.** Gone without your consent.
- **Disposed.** Thrown away or recycled.
- **Other.** Anything the list does not cover.

Owned, Ordered, and Loaned out count as "still held." The others record copies that have left the collection but whose history you want to keep.

## Where the money lives

You may notice there is no "price paid" field on the copy. That is deliberate. What you paid, and when you acquired the copy, come from its **transactions**, and what it is worth over time comes from its **valuations**. This keeps the full money story instead of a single overwritten number. Start with @doc(copies.recordPaymentsAndValue).

## Where to next

- Understand the records a copy can carry: @doc(copyHistory.concept, "A copy's history explained").
- Record the purchase: @doc(copies.recordPaymentsAndValue).
- Keep its address current: @doc(copies.move).
