---
id: account.freePlan
title: The free plan and unlocking your account
slug: free-plan-and-unlocking
section: account-and-profile
---

# The free plan and unlocking your account

If you run KolleK yourself, this page does not apply to you. A self hosted instance has no item limit, no upgrade screen and nothing to buy. Skip ahead to @doc(selfHosting.index) instead.

On a hosted instance, the one we run for people who would rather not run a server, an account starts on a free plan. This page explains how much that plan holds, what happens when you fill it, and what unlocking involves.

## How much the free plan holds

A free account holds **ten items**, counted across every collection you have. Two collections of five items each fill the plan just as surely as one collection of ten.

Past those ten, KolleK accepts **five more** as a grace. Nothing about the account changes at that point except the tone of the screens: it tells you plainly that you have outgrown the plan, and keeps working.

At **fifteen items** the account stops growing. You cannot add a sixteenth until the account is unlocked.

Only items are counted. Collections, categories, tags, locations, series, sets, photos, documents and the people you invite are not, and you can keep creating all of them. Adding a copy to an item you already have does not count either, since copies belong to an item rather than standing on their own. See @doc(items.itemsVsCopies) if that distinction is new to you.

:::note
Nothing you have already catalogued is ever deleted, hidden or made read only. Every item, photo, valuation and loan record stays exactly where it is, and stays searchable and exportable. The only thing that stops is adding something new.
:::

## What you will see as you fill it

**The usage card in the sidebar.** From your first item onwards, the bottom of the sidebar shows how much of the plan you have used, as a bar and a count. Below ten it simply tells you how many items are left. Past ten it turns to a warning and tells you how far over you are.

**A banner on the collection screen.** Once you go past ten items, opening any collection shows a banner explaining where you stand. Inside the grace it tells you how many items you can still add. At fifteen it tells you that adding is paused.

**The Add item button stops working.** At fifteen items, the **Add item** button on a collection is disabled rather than hidden, so it is clear that the button still exists and only the allowance has run out. Everything else on the screen keeps working: you can still edit items, add copies, upload photos and record loans.

## Unlocking the account

The banner and the usage card both lead to the **Plan and billing** screen, which lays out the two honest ways forward.

The first is to unlock the account with a single payment. It removes the item limit for good. There is no subscription, no renewal and no second invoice, and everything you have already catalogued carries over untouched.

The second is to move to self hosting, which is free, runs the same code and has no item limit at all. We put both on the same screen deliberately. See @doc(kollek.hostingOptions) for the trade-offs between them.

:::note
Anyone in the account can read the **Plan and billing** screen, because anyone can run into the limit. Only an owner can go further and unlock the account. If you are an editor or a viewer, ask an owner to take it from there. See @doc(accounts.usersAndRoles).
:::

## The confirmation step

Choosing to unlock takes an owner to a confirmation screen before any payment. It exists because the payment is **final and non refundable**, and we would rather say that on a screen you have to read than in terms you will not.

The screen asks you to tick four boxes, each one a separate point to agree to:

- that this is a single payment and it is not refundable
- that you will email support rather than open a chargeback with your bank if something goes wrong
- that you know self hosting is free and are choosing the hosted account on purpose
- that you have looked at what the unlock includes and it covers what you need

All four have to be ticked before you can continue. What you confirmed is recorded, along with who confirmed it, when, and the address the confirmation came from. Whoever runs the instance can see that record on the account. It is kept as evidence of what was agreed, not used for anything else.

:::warning
Once payment is possible and you complete it, there is no refund. Decide before you pay, not after. If you are unsure, the free plan and self hosting both give you room to make up your mind at no cost.
:::

## Checkout is not open yet

The limit is enforced today, and both screens above are finished, including the confirmation step and its record. The payment processor behind them is not connected yet, so there is currently no way to actually pay or to unlock an account.

If you reach the limit before checkout opens, your options are to remove items you no longer want to track, or to move to a self hosted instance, which is free and unlimited. See @doc(troubleshooting.featureStatus) for the current state of this and everything else that is still on its way.

## Where to next

- Weighing the two ways to run KolleK? Read @doc(kollek.hostingOptions).
- Ready to run it yourself instead? Start with @doc(selfHosting.installDocker).
- Wondering what else counts against nothing at all? See @doc(accounts.settings).
