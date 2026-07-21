---
id: tutorials.trackValuableItem
title: "Tutorial: Track the full life of a valuable item"
slug: track-a-valuable-item
section: tutorials
---

# Tutorial: Track the full life of a valuable item

Most items need a condition, a location, and maybe a price. A genuinely valuable one deserves more: proof of what you paid, a professional opinion of its worth, insurance, the paperwork to back all of it, and a record of everywhere it goes and everything done to it. KolleK records each of those as its own dated entry on the copy, and this tutorial exercises all of them on a single piece.

We will follow Priya, who has just bought the best watch in her collection, a 1968 chronograph. By the end, its copy will carry a transaction, a valuation, an insurance record, two documents, a completed loan, a maintenance record, and a provenance narrative, all readable as one timeline.

This is the longest tutorial. Do it with a real item of your own, or just read it to see how the pieces fit.

## Before you start

- Finish [Catalogue your first collection end to end](2-catalogue-your-first-collection.md) first. This tutorial assumes the core loop is second nature.
- Read [A copy's history explained](../3-core-concepts/11-copy-history.md). It is the map for everything below.
- Remember the two rules that keep the model coherent: money only ever lives in transactions, and revaluing or re insuring writes a new record instead of overwriting the old one.

## Step 1: Catalogue the item and its copy

Priya creates the item "Heuer Carrera 2447" in her Watches collection, which uses the ready made **Watches** type. She fills in the type's fields: **Brand**, **Model**, **Movement** (Automatic, Quartz, or Manual), and answers **Box & Papers** with yes.

Then she adds the copy, and one field matters more than usual here:

- **Identifier.** She enters the watch's serial number. For valuable items this is what ties your record to the physical object, the same way a slab number does for a graded comic.
- **Condition** and **location**, as always.

Everything that follows happens on this copy's **History** tab, which shows one copy at a time.

## Step 2: Record the acquisition

::::steps
:::step title="Add the purchase transaction"
In the copy's history, add a **transaction** of type **Purchase**. Priya enters the amount, the auction house as the **counterparty**, the **date**, the buyer's premium under **fees**, and the lot number as the **reference**.

::screenshot{label="Transaction form filled in for an auction purchase"}
:::
::::

Why this matters: this single record gives the copy its price paid and its acquisition date, anchors the statistics, and will later anchor the provenance narrative. Get it right and everything else hangs off it. The details are in [Record what you paid and what it is worth](../5-copy-history/2-record-payments-and-value.md).

## Step 3: Add a professional valuation

Priya has the watch appraised. She adds a **valuation** with the type **Professional appraisal**, the appraised amount, the confidence set to **High**, and the appraiser's name as who valued it.

:::note
Next year she will have it appraised again and add a new valuation. The old one stays. The copy's estimated value is always its most recent valuation, and the sequence of valuations is how you will one day chart its worth over time.
:::

## Step 4: Insure it

With a professional appraisal in hand, insurance is the obvious next step. Priya adds an [insurance record](../5-copy-history/3-insure-a-copy.md): the **provider**, the **insured value**, the **policy number**, the **coverage type**, the **deductible**, the **start and end dates**, whether it is a **scheduled item** on the policy, and the insurer's contact details. She leaves the status **Active**.

When the policy renews, she will add a new record and mark this one **Expired**. Expired and cancelled records stay visible as dimmed history behind the current one, which is exactly what you want when a claim asks what cover existed in a given year.

## Step 5: Attach the paperwork

Records are claims. Documents are proof. Priya scans two pieces of paper and [attaches them](../5-copy-history/8-attach-documents.md) where they belong:

::::steps
:::step title="Attach the receipt to the transaction"
On the purchase transaction, she attaches the auction invoice as a document of type **Receipt**, with its issued date and invoice number as the reference.
:::

:::step title="Attach the appraisal to the valuation"
On the valuation, she attaches the appraiser's report as a document of type **Appraisal**.
:::
::::

A document can be an uploaded file (PDF, images, Word, Excel, CSV, or plain text, up to 20 MB) or an external link if the paperwork lives elsewhere. Attaching each document to the record it proves, rather than loosely to the copy, is what makes the story auditable later.

## Step 6: Lend it to an exhibition, and get it back

A local horology society asks to display the watch for a month. Custody is exactly what [loans](../5-copy-history/4-lend-and-borrow-copies.md) track.

::::steps
:::step title="Record the outgoing loan"
Priya creates a **loan** with the direction **Lent out**, the society as the party, "Exhibition" as the purpose, the loan and due dates, and the watch's condition as it left her hands.
:::

:::step title="See the copy's status change"
While the loan is open, the copy reads as loaned out. It is still hers, custody moved, not ownership. If the due date passed without a return, KolleK would flag the loan overdue automatically.
:::

:::step title="Record the return"
When the watch comes back, she records the **return**, which captures the return date and the condition it came back in. Comparing the condition out and the condition in is how transit damage becomes visible instead of debatable.
:::
::::

## Step 7: Log the servicing

Before the watch went on display, Priya had it serviced. She adds a [maintenance record](../5-copy-history/5-record-maintenance-and-repairs.md) of type **Servicing**: a title, the watchmaker who performed it, the date, the cost, the condition before and after, and a **next due date** five years out so the app can surface the next service when it approaches. Since a full service on a vintage movement is significant, she chooses to include it in the copy's provenance.

## Step 8: Build the provenance narrative

Finally, the ownership story. Priya knows the watch's past from the auction catalogue, and she records it as [provenance events](../5-copy-history/6-trace-provenance.md), oldest first:

- An **Origin** event for its manufacture, dated to the year 1968.
- An **Ownership transfer** to the original owner's family, with the date precision set to **Approximate**, because the catalogue only says "circa 1975".
- An **Exhibition** event for the society display she just completed.
- Her own **Acquisition**, dated exactly, linked to the purchase transaction from step 2.

Two things to notice. Date precision exists because provenance is often uncertain, an event can be dated exactly, to a month, to a year, approximately, or left unknown, and it displays accordingly. And provenance events carry no amounts: an event tied to a purchase or sale links to its transaction, so money stays in exactly one place.

## Step 9: Read the whole story

Open the copy's **timeline**. Everything you just recorded, the purchase, the valuation, the insurance, the documents, the loan out and back, the servicing, and the provenance events, reads as one chronological story. The default view keeps to the meaningful entries, and the complete view adds the routine ones. [Read the copy timeline](../5-copy-history/9-read-the-copy-timeline.md) explains the view in full.

This is the payoff: one screen that answers what the watch cost, what it is worth, who has held it, what has been done to it, and what proves all of the above.

## Common mistakes to avoid

- **Recording the purchase price as a valuation.** It is a transaction. The distinction is the backbone of the whole model.
- **Editing old records instead of adding new ones.** A new appraisal is a new valuation, a renewed policy is a new insurance record. History only works if it accumulates.
- **Leaving documents unattached.** A receipt filed on the transaction it proves is evidence. A file loosely attached to the copy is a scan you will have to re identify later.

## Where to next

- Every record type used here has its own detailed guide in the [copy history section](../5-copy-history/1-introduction.md).
- See how these records feed the numbers in [Understand your collection statistics](../7-insights/2-collection-statistics.md).
- Sharing the collection with others? [Invite your household or club](5-invite-your-household.md).
