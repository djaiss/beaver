# Tutorial: Catalogue your first collection end to end

In this tutorial you will take a brand new account all the way to a real, populated collection. You will create a collection, look at the custom fields it records, add an item with a cover photo, record the physical copy you own, capture what you paid for it, add a first valuation, and read the statistics that result.

We will follow Emma, who collects comics. She catalogued one item quickly in the [five minute quick start](../getting-started/quick-start.md). This time she does it properly, and by the end her catalogue knows what her comic cost, what it is worth, and where it lives.

Expect this to take twenty to thirty minutes.

## Before you start

- You need an account you can sign into. If you do not have one, [create your account](../getting-started/create-your-account.md) first.
- You should know the difference between an item and a copy. If you are not sure, read [Items and copies](../core-concepts/items-and-copies.md) now. The tutorial leans on it constantly.
- Have one real thing you own ready to catalogue, ideally with a photo and a rough memory of what you paid for it.

## Step 1: Create the collection

Every item lives inside a [collection](../core-concepts/collections.md), so that is where everything begins.

::::steps
:::step title="Start a new collection"
From your dashboard, choose **New collection**.

::screenshot{label="Dashboard with the New collection button"}
:::

:::step title="Name it and give it a face"
Emma names hers "My Comics", picks the 📚 emoji, and writes a one line description. The emoji and description are optional, but they make the collection easy to spot later.
:::

:::step title="Choose the collection type"
Enable the ready made **Comics** type for this collection. The type is what decides which custom fields items in this collection can record.
:::

:::step title="Leave visibility and currency alone for now"
A new collection is **private** by default, meaning only you can see it, and it uses your account's default currency. Both can be changed later. Save the collection.
:::
::::

Why this matters: the choices on this form shape everything downstream. The type controls the fields you fill in for each item, and the currency controls how money on this collection's copies is displayed.

## Step 2: Look at what the Comics type records

Your account arrived with a dozen ready made [collection types](../core-concepts/collection-types-and-custom-fields.md). Before adding items, it is worth seeing what the Comics type will ask you for, so nothing on the item form surprises you.

Open the collection types settings and select **Comics**. You will find:

- **My Rating**, a five star rating field.
- A **Publishing info** group: Issue # (a number), Publisher (a choice of Marvel, DC, Image, Dark Horse, or Independent), Writer, Artist, and Cover Date.
- A **Condition & grading** group: Variant and Signed, both yes or no questions.

You do not have to change anything. If you want to add or reorder fields, the [type setup guide](../organizing/set-up-collection-types-and-custom-fields.md) covers it. For this tutorial the defaults are exactly what Emma needs.

## Step 3: Add the item with its details and photo

Now the satisfying part. Open your new collection.

::::steps
:::step title="Create the item"
Choose to add a **New item** and give it a **name**. Emma types "Amazing Spider-Man #300".
:::

:::step title="Fill in the custom fields"
Because the collection uses the Comics type, the form offers the fields you just reviewed. Emma sets **Issue #** to 300, **Publisher** to Marvel, and answers **Signed** with no. Fill in what you know and skip the rest. Empty fields are fine.

::screenshot{label="Item form showing the Comics custom fields"}
:::

:::step title="Upload a cover photo"
Add a **photo** of the item. JPEG, PNG, WebP, and GIF files up to 10 MB are accepted. If you add several, mark the best one as the main photo. It becomes the cover you will recognize in every list.
:::
::::

Why this matters: descriptive details like issue number and publisher belong on the item, because they are true of every copy of that comic in the world. Nothing you typed so far says anything about the physical copy in Emma's hands. That is next.

## Step 4: Record the copy you own

An item without a [copy](../core-concepts/items-and-copies.md) is just an entry in an encyclopedia. The copy is the physical thing you own.

::::steps
:::step title="Add a copy to the item"
On the item, add a **copy**.
:::

:::step title="Grade it and shelve it"
Set its **condition**. Emma picks **Used** from the ready made list (New, Like New, Used, Worn, and Damaged come with every account). Then set its **location**. Emma keeps hers in **Storage**, one of the default locations, though you can [build your own location map](../organizing/set-up-your-locations.md) any time.
:::

:::step title="Check the status"
Leave the status as **Owned**. The other statuses (Ordered, Loaned out, Sold, and so on) exist for copies that are not sitting on your shelf right now. Save.
:::
::::

:::note
If you own two of the same comic, do not create a second item. Add a second copy to this one. Each copy carries its own condition, location, money, and history.
:::

## Step 5: Record what you paid

Here is where KolleK goes beyond a list. Money never lives on the item or in a note. It lives in a **transaction** on the copy, so your records stay precise as they grow. The full explanation is in [Record what you paid and what it is worth](../copy-history/record-payments-and-value.md).

::::steps
:::step title="Open the copy's history"
Open the item's **History** tab. It shows one copy at a time, and you only have one so far.
:::

:::step title="Add a purchase transaction"
Add a **transaction** of type **Purchase**. Emma enters the amount she paid, the shop as the counterparty, and the date she bought it. Taxes, fees, shipping, and a reference are there if you need them.

::screenshot{label="New transaction form with the Purchase type selected"}
:::
::::

Why this matters: the purchase transaction is what gives the copy its **price paid** and its **acquisition date**. The statistics you will see in step 7 use that date to chart how your collection grew.

## Step 6: Add a first valuation

What you paid and what it is worth are different facts, and KolleK keeps them apart on purpose. Worth is recorded as a **valuation**.

Still in the copy's history, add a **valuation**. Emma chooses the type **Own estimate**, enters what she believes the comic would fetch today, and sets the confidence to **Medium**. When she gets it professionally appraised one day, she will add a new valuation rather than edit this one, and the old estimate will remain as history.

:::note
A purchase price is a transaction, never a valuation. The copy's estimated value always comes from its most recent valuation, and its price paid comes from its earliest acquiring transaction.
:::

## Step 7: See what you built

Open the collection. You should see:

- Your item with its cover photo, in the grid view.
- An item count of one, and a total value matching your valuation.

Now open the collection's **statistics**. Even with one item there is something to read: the total estimated value, the value by location, and the acquisition falling in the month you bought it. The [statistics guide](../insights/collection-statistics.md) explains where every number comes from.

## What you accomplished

You exercised the entire core loop of KolleK: a collection with a type, an item with details and a photo, a copy with a condition and a location, a transaction holding the money, and a valuation holding the worth. Every feature in the product builds on the records you just created.

## Common mistakes to avoid

- **Creating duplicate items instead of copies.** Two of the same comic are one item with two copies.
- **Typing the purchase price as a valuation.** The price you paid is a Purchase transaction. Valuations are for what it is worth now.
- **Putting copy details on the item.** Condition, location, and money always belong to a copy, because a second copy will differ in all three.

## Where to next

- Tailor the account to your actual hobby in [Set up your account for a specific hobby](set-up-for-your-hobby.md).
- Go deep on one prized piece in [Track the full life of a valuable item](track-a-valuable-item.md).
- Cataloguing with family or friends? See [Invite your household or club](invite-your-household.md).
