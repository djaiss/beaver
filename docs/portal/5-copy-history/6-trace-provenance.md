---
id: copies.traceProvenance
title: Trace a copy's provenance
slug: trace-provenance
section: copy-history
---

# Trace a copy's provenance

Provenance is the story of where a copy came from: who has owned it, where it has been shown, when it was authenticated, and how it reached you. For valuable or historically interesting pieces, that story is part of the value. KolleK lets you build it as a sequence of dated provenance events that reads, oldest first, as a narrative.

Unlike the other records in this section, provenance often reaches back long before you owned the copy, into decades you only half know. The model is built for that uncertainty.

## What a provenance event records

Each event has a **type**, a **title**, and as much context as you have: the **parties** involved, the **location**, a **reference** (a catalogue number, an auction lot, an archive entry), and a **date**.

The event types cover the life of an object: **Acquisition**, **Sale**, **Gift**, **Inheritance**, **Ownership transfer**, **Custody transfer**, **Loan**, **Return**, **Exhibition**, **Authentication**, **Appraisal**, **Significant restoration**, **Origin**, **Discovery**, and **Other**.

Two of them anchor the ends of the story. **Origin** records where the object began (its manufacture, its printing, its minting). **Discovery** records the moment it surfaced, when that is a story of its own.

## Dates you are not sure about

Provenance dates are often approximate, and pretending otherwise would corrupt the story. Every event carries a **date precision** alongside its date:

- **Exact date**. You know the day.
- **Month**. You know the month and year.
- **Year**. You know the year only.
- **Approximate**. A best estimate. Read it as circa.
- **Unknown**. The event happened, but you cannot date it.

The event displays according to its precision, so "circa 1970" and "March 1970" look as certain as they actually are.

## The money rule

:::note
Provenance events carry no amounts. Money always lives in transactions. An event tied to a purchase or sale links to its transaction instead, so the narrative and the accounting never drift apart.
:::

This is the same rule you met in [Record what you paid and what it is worth](2-record-payments-and-value.md), applied from the other side.

## Build a provenance narrative

Priya's 1968 Omega Speedmaster came with a folder of paperwork from the auction house. She reconstructs its story.

::::steps
:::step title="Open the copy's history"
Open the item, go to its **History** tab, select the copy, and open the **Provenance** section.

::screenshot{label="History tab with the Provenance section open"}
:::

:::step title="Start at the origin"
Add an **Origin** event: "Manufactured, Bienne, Switzerland", dated 1968 with **Year** precision.
:::

:::step title="Add what the paperwork supports"
Add an **Ownership transfer** for the first known owner, dated **Approximate** to the early 1970s, with the party's name from the service papers. Add an **Authentication** event for the extract from the manufacturer's archives, with the extract number as the **reference**.
:::

:::step title="End with your acquisition"
Add an **Acquisition** event for her own purchase, dated exactly, and link it to the purchase transaction she already recorded. The price lives on the transaction, not here.
:::
::::

Read top to bottom, the section now tells the watch's story from Swiss workshop to Priya's collection.

## Verified or family legend

Each event carries a **verified** flag with a note for how it was verified. Use it honestly. An archive extract is verified evidence. "My grandfather always said he bought it in Geneva" is a real part of the story too, but it stays unverified, and the narrative is stronger for admitting the difference.

## Events that arrive on their own

Some provenance builds itself. A [loan](4-lend-and-borrow-copies.md) marked as part of provenance adds matching loan and return events, and a [maintenance record](5-record-maintenance-and-repairs.md) flagged as significant appears as a restoration event. You assemble the deep past; the present documents itself as it happens.

## Where to next

- Attach the archive extract or certificate to its event in [Attach documents to a copy](8-attach-documents.md).
- Record the purchase the acquisition event links to in [Record what you paid and what it is worth](2-record-payments-and-value.md).
- Read the finished story in [Read the copy timeline](9-read-the-copy-timeline.md).
