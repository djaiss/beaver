# Tutorial: Set up your account for a specific hobby

Adding one item is easy. Adding two hundred is only easy if the account is prepared first. In this tutorial you will tailor KolleK to a specific hobby before mass data entry: shape the collection type and its custom fields, build a location map that mirrors your real space, and seed a tag vocabulary, so that every item you add afterwards is fast and consistent.

We will follow Noah, who is about to catalogue roughly three hundred vinyl records. The same approach works for any hobby, so substitute your own as you go.

Expect this to take about half an hour, and to save you many hours later.

## Before you start

- Finish [Catalogue your first collection end to end](catalogue-your-first-collection.md) or at least the [quick start](../getting-started/quick-start.md), so the core loop is familiar.
- Know the concepts behind [collection types and custom fields](../core-concepts/collection-types-and-custom-fields.md), [locations](../core-concepts/locations.md), and [tags](../core-concepts/tags.md). Skim those pages if not.
- Have a think about what you actually want to record for each item. Ten minutes with a notepad beats reworking fields after fifty entries.

## Step 1: Shape the collection type

Noah starts with the ready made **Vinyl Records** type that came with his account. It already records My Rating, a **Release info** group (Artist, Album, Release Year), and a **Pressing details** group (Pressing/Edition, Speed, Color Vinyl).

That is close to what he wants, but he buys a lot of Japanese pressings and cares about the condition of sleeves. So he adjusts the type.

::::steps
:::step title="Open the type"
Go to collection type settings and select **Vinyl Records**. The editor saves as you go, so there is no save button to hunt for.

::screenshot{label="Collection type editor showing the Vinyl Records fields"}
:::

:::step title="Add the fields you will actually use"
Noah adds a **Country of Pressing** text field to the Pressing details group, and a **Sleeve Condition** field as a select with the options he grades by. The available field types are text, number, date, yes or no, select, and rating (up to five stars).
:::

:::step title="Group and order the fields"
Create a new group if a set of fields belongs together, and drag fields into the order you want them on the item form. Groups exist purely to keep long forms readable.
:::
::::

Why this matters: custom fields defined now appear on every item form in any collection that uses this type. Deciding them up front means three hundred consistent records instead of three hundred improvised ones.

:::note
Design fields for the questions you will ask later. "Which records are colored vinyl" is answerable only if Color Vinyl is a field. A detail buried in a description cannot be scanned.
:::

## Step 2: Build your location map

Noah keeps records in two places: a listening room with three shelves, and crates in storage. He models exactly that, because a location in KolleK is only useful if it matches a place you can physically walk to.

::::steps
:::step title="Create the top level places"
In [location settings](../organizing/set-up-your-locations.md), create **Music Room** 🛋️ and **Storage** 📦. These are the rooms.
:::

:::step title="Nest the real subdivisions"
Under Music Room, create **Shelf A**, **Shelf B**, and **Shelf C**. Under Storage, create **Crate 1** and **Crate 2**. Locations nest as deeply as you need, so a box inside a crate inside a room is fine.
:::
::::

Why this matters: every copy points at one location, and later moves are recorded as [location history](../copy-history/move-a-copy.md). A good map now means "where is that record" always has an exact answer.

## Step 3: Seed your tag vocabulary

Tags cut across collections and hierarchies, which makes them ideal for the labels that do not fit anywhere else. Noah creates his starting set from [tag settings](../organizing/manage-account-tags.md): **Signed**, **First Pressing**, **Japanese Pressing**, **To Sell**, and **Needs Cleaning**.

Two habits make tags stay useful:

- Keep them few and reusable. A tag used once is a fact that belonged in a field or a note.
- Agree on spelling before others join. "Signed" and "Autographed" as separate tags will haunt you.

You can always create a tag on the spot while editing an item, so this list only needs to cover the labels you already know you want.

## Step 4: Import a type instead of building one

There is a shortcut worth knowing. A collection type can be [exported and imported as JSON](../organizing/import-and-export-a-collection-type.md). If a friend has already built a great Vinyl type, they can export it, and you can import it by pasting the JSON, bringing over the name, color, groups, fields, and select options in one step.

:::note
Importing a type brings the type definition only. It does not import items or their data. There is currently no item or whole collection import, and the honest state of that is tracked on the [feature status page](../troubleshooting/feature-status.md).
:::

Noah imports a "45 RPM Singles" type a club friend shared, and it appears next to his own types, ready to attach to a collection.

## Step 5: Create the collection and connect everything

Now the pieces come together.

::::steps
:::step title="Create the collection"
Noah creates a collection named "Vinyl", picks the 💿 emoji, and writes a short description.
:::

:::step title="Enable the types it needs"
He enables both the **Vinyl Records** type and the imported **45 RPM Singles** type. A collection can use several types, and each item picks the one that fits it.
:::

:::step title="Set the currency"
He sets the collection currency to the one he actually buys records in. It can differ from the account default, and all money on this collection's copies will display in it.
:::
::::

## The result

Add one record now and feel the difference: the form asks exactly the right questions, the location dropdown offers real shelves, and the tags you need already exist. From here, mass data entry is a rhythm rather than a series of decisions.

## Common mistakes to avoid

- **Over designing fields.** Ten fields you fill in beat twenty five you skip. You can add fields later; retro filling them is the tedious part.
- **Locations that do not match reality.** If there is no physical Shelf B, the location "Shelf B" will drift out of date immediately.
- **Using tags for what fields do better.** A grade, a year, or a rating belongs in a custom field where it can be a real value, not a label.

## Where to next

- Start entering items with [Add and edit items](../core-features/add-and-edit-items.md).
- Track your most valuable piece properly in [Track the full life of a valuable item](track-a-valuable-item.md).
- Working with others? [Invite your household or club](invite-your-household.md).
