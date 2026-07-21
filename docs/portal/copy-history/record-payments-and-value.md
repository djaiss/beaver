# Record what you paid and what it is worth

Money and worth are the two questions collectors ask most, and KolleK keeps them deliberately separate. A **transaction** records money actually changing hands. A **valuation** records what a copy is worth at a point in time, whether or not any money moved. This page shows you how to record both, and explains the rule that keeps them straight.

If you have not read [A copy's history explained](../core-concepts/copy-history.md), read it first. It introduces the idea that these records are append only history, not fields you overwrite.

## The rule that keeps everything straight

A purchase price is a transaction, not a valuation.

When Priya buys a 1968 Omega Speedmaster for 4,200, that is a **Purchase** transaction. It records what she paid on that day, and it never changes. What the watch is *worth* is a separate question that changes over time, and each answer is its own valuation.

KolleK derives two figures from these records automatically:

- A copy's **estimated value** is the amount of its most recent valuation. A copy with no valuations reads as unvalued, not as worth zero.
- A copy's **price paid** and **acquisition date** come from its earliest acquiring transaction (a Purchase, Trade, Gift received, or Inheritance).

You never type these figures directly on the copy. You record the history, and the current numbers follow from it.

## Record a transaction

A transaction covers any money or ownership movement around a copy: buying it, selling it, trading it, paying a fee, or shipping it somewhere.

::::steps
:::step title="Open the copy's history"
Open the item, go to its **History** tab, and select the copy you want. Then open the **Transactions** section.

::screenshot{label="History tab with the Transactions section open"}
:::

:::step title="Add a transaction"
Choose to add a transaction and pick its **type**: Purchase, Sale, Trade, Gift received, Gift given, Inheritance, Refund, Fee, Tax, Shipping, or Other.
:::

:::step title="Enter the money"
Fill in the **amount**, and optionally **taxes**, **fees**, and **shipping** so the true total cost is captured, not just the sticker price.
:::

:::step title="Add the context"
Record the **counterparty** (who you bought from or sold to), the **date**, and a **reference** such as an order or auction lot number. Save the transaction.
:::
::::

Priya records her Speedmaster purchase: type **Purchase**, amount 4,200, fees 120 for the auction house, counterparty "Fine Time Auctions", and the lot number as reference. That one record now answers what she paid, when she acquired it, and where it came from.

:::note
The earliest acquiring transaction (Purchase, Trade, Gift received, or Inheritance) is what gives the copy its acquisition date. Copies without one are counted as undated in your statistics, so record it even for things you bought long ago, with your best guess at the date.
:::

## Record a valuation

A valuation answers "what is this worth right now, and how sure am I."

::::steps
:::step title="Open the Valuations section"
From the same **History** tab, with your copy selected, open the **Valuations** section.
:::

:::step title="Add a valuation"
Pick a **valuation type**: Own estimate, Professional appraisal, Market estimate, Insurance value, Auction estimate, Automated estimate, or Other.
:::

:::step title="Enter the value and your confidence"
Fill in the **amount**, pick a **confidence** level (Low, Medium, High, or Unknown), and record **who valued it**. Save it.

::screenshot{label="New valuation form with type, amount, and confidence"}
:::
::::

Two years later, a dealer tells Priya the Speedmaster would fetch around 5,500. She adds a new valuation: **Market estimate**, 5,500, confidence **Medium**, valued by the dealer. Her original valuation stays in the history, and the copy's estimated value updates to the new figure.

:::note
Revaluing always writes a new valuation. You never edit the old one to a new number, so you keep a genuine record of how the value moved over time. That history is what draws the value over time chart in your statistics.
:::

## Where these numbers surface

The figures you record here feed the rest of KolleK: the total value shown on each collection, the value over time and acquisitions charts in [collection statistics](../insights/collection-statistics.md), and the top items by value. Thorough transactions and valuations are what make those screens trustworthy.

## Where to next

- Keep the paperwork with the record. [Attach documents to a copy](attach-documents.md), such as the receipt on a transaction or the appraisal on a valuation.
- Insuring the copy for that value? [Insure a copy](insure-a-copy.md).
- Building the full ownership story? [Trace a copy's provenance](trace-provenance.md).
