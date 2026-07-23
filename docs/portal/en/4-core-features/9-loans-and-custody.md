---
id: loans.custody
title: Loans and custody
slug: loans-and-custody
section: core-features
---

# Loans and custody

A loan is a temporary move of custody without any move of ownership. When you lend a piece to a friend, a gallery, or a museum, you still own it. When you borrow a piece in, someone else still owns it. The **Loans** section is the account wide view of everything that is currently out of your hands or in your hands on loan.

Reading the section is open to any role. Recording, returning, editing, and deleting a loan requires the **editor** or **owner** role.

## The two directions

Every loan points one of two ways, and the section shows one direction at a time. Use the toggle at the top to switch between them.

- **Lent out.** A piece of yours that someone else is holding. While an outgoing loan is active or overdue, its copy reads as **Loaned** in your collection, because it is not physically with you.
- **Borrowed in.** A piece someone else owns that you are holding for now. A borrowed piece never changes how your own copies read, because you never owned it.

## What the tabs show

Within a direction, the tabs slice the same loans different ways.

- **All loans.** Every loan in the direction, with a search box and filters for collection, status, and sort order.
- **Due and overdue.** Three lists: loans past their due date, loans falling due within thirty days, and open ended loans that have no due date at all.
- **Risk and exceptions.** The loans that need a second look: overdue, lost, returned in worse condition, missing a due date, missing a condition on the way out, or lent out with no documents on file.
- **By party.** One card per person or institution, so you can see everything a single borrower or lender has at once.
- **Deposits.** What you hold or are owed across open loans, and the loans that carry a deposit.
- **Timeline.** Upcoming due dates, recently returned pieces, and recently loaned pieces.

The stat tiles across the top are shortcuts: each one opens the tab that answers it.

## Record a loan

You can start a loan straight from the section, without hunting for the copy first.

::::steps
:::step title="Open the new loan drawer"
Choose **New loan**. Pick the direction, then walk down from collection to item to the exact copy that is moving.
:::

:::step title="Name the party and the dates"
Enter who the piece is going to or coming from, the date it left, and a due date. Tick **open ended** when there is no agreed return date.
:::

:::step title="Record condition and any deposit"
Pick the **condition out** so a later return can be compared against it, and record a **deposit** if one changed hands. The deposit currency defaults to the collection's currency.
:::

:::step title="Mark it for provenance if it belongs to the story"
Tick **include in provenance** for an institutional loan or an exhibition, and a matching provenance event is generated. Leave it off for an informal personal loan, which stays in loan history only.
:::
::::

### One open loan per copy

A physical copy can only be in one place at a time, so a copy may have at most one **open outgoing** loan. If you try to lend out a copy that is already out, the section blocks it and asks you to return the current loan first. This rule holds in the JSON API too.

## Return a loan

Closing a loan is its own step, not an edit, so it captures what an edit would not.

::::steps
:::step title="Open the loan and mark it returned"
Open the loan from any list, then choose **Mark as returned**.
:::

:::step title="Record the return"
Enter the date the piece came back and the **condition in**. Setting a condition in updates the copy's current condition, and brings the copy back into your custody.
:::
::::

When the condition in is worse than the condition out, the loan is flagged as possible damage, both on the loan itself and in the **Returned worse** risk list.

## Export what is out

The **Export what's out** button downloads a CSV of the open loans in the current direction, so you have a plain list of what is currently in someone else's hands, or in yours.

## Related

- Loans also appear on a copy's own history. See @doc(copies.track) for the copy record they hang off.
