---
id: copyHistory.concept
title: A copy's history
slug: copy-history
section: core-concepts
---

# A copy's history

This page explains the conceptual heart of KolleK: a copy shows its current state, while everything that ever happened to it lives in separate, dated records. Understand this once and the whole Tracking section becomes a set of obvious tasks.

## Current state versus history

Look at one of Priya's watches. The copy tells you its current state at a glance: its @doc(conditions.overview, "condition") is Used, its current @doc(locations.overview, "location") is the display case, its estimated value is what the last appraisal said.

None of that is typed in as a plain fact that overwrites the previous one. Each is the visible tip of a record underneath:

- The estimated value is her **most recent valuation**.
- The price she paid, and the date she acquired it, come from her **earliest acquiring transaction**.
- The current location is the **open entry in its location history**.

The copy is a summary. The records are the truth.

## The record types

Seven kinds of dated records can hang off a copy, each with its own purpose and its own how to page:

- **Transactions** record money and ownership changes: what you paid, what you sold for, fees, shipping. See @doc(copies.recordPaymentsAndValue).
- **Valuations** record what the copy was worth at a point in time, and who said so. Same page as transactions, because the two are easy to confuse.
- **Insurance records** capture coverage: provider, insured value, policy dates. See @doc(copies.insure).
- **Loans** track custody when a copy leaves your hands or arrives from someone else. See @doc(loans.lendAndBorrow).
- **Maintenance records** log cleaning, repair, and conservation work. See @doc(copies.recordMaintenance).
- **Provenance events** build the ownership and authenticity story. See @doc(copies.traceProvenance).
- **Location history** remembers every place the copy has lived. See @doc(copies.move, "Move a copy").

You can also @doc(copies.attachDocuments, "attach documents") (receipts, appraisals, certificates) to the copy or to any individual record, and read everything merged together on @doc(copyHistory.readTimeline, "the copy timeline").

## The two rules that keep it coherent

**Money only ever lives in transactions.** A purchase price is a transaction. A sale is a transaction. Valuations and provenance events describe worth and story, never payment.

**History is append only.** Revaluing a copy writes a new valuation next to the old one. Renewing insurance writes a new record. Nothing overwrites the past, which is why the timeline can tell the whole story years later.

:::note
If you find yourself editing an old record to reflect something new, stop and add a new record instead. Editing is for correcting mistakes, not for updating reality.
:::

## Do you need all of this?

No. Emma catalogues most comics with just a copy, a condition, and a location. The history records earn their keep on the pieces that matter: the valuable, the insured, the loaned, and the inherited. Use as much or as little as each copy deserves.

## Where to next

- Start with the money in @doc(copies.recordPaymentsAndValue).
- See the whole story in one view in @doc(copyHistory.readTimeline).
- Follow one prized piece end to end in the tutorial @doc(tutorials.trackValuableItem, "Track the full life of a valuable item").
