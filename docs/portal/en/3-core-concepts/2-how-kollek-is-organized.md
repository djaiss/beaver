---
id: kollek.howOrganized
title: How KolleK is organized
slug: how-kollek-is-organized
section: core-concepts
---

# How KolleK is organized

This page gives you the whole map before any detail. Everything else in this section zooms into one part of it.

## The spine: four levels

Everything you catalogue in KolleK lives in a simple nesting:

- An **@doc(accounts.usersAndRoles, "account")** is your workspace. Everything below belongs to exactly one account.
  - A **@doc(collections.overview, "collection")** is a named group of things, such as "My Comics" or "Wine Cellar".
    - An **@doc(items.itemsVsCopies, "item")** is a kind of thing, such as "Amazing Spider-Man #1".
      - A **@doc(items.itemsVsCopies, "copy")** is one physical instance of that item that you actually own.

Emma's account holds her "My Comics" collection. Inside it is the item "Amazing Spider-Man #1". She owns two of them, so the item has two copies, each with its own condition, storage spot, and value.

The item and copy split is the heart of the model, and it gets @doc(items.itemsVsCopies, "its own page"). If you read only one concept page, read that one.

## The shared helpers

Around the spine sit a few account wide tools. They are defined once and reused everywhere:

- **@doc(collectionTypes.overview)** decide what details each kind of item records. A Comics type asks for an issue number, a Wine type asks for a vintage.
- **@doc(organizing.categoriesSetsAndSeries)** group items in three different ways: filing within a collection, tracking a finite list to completion, and tying a franchise together across collections.
- **@doc(tags.overview)** are free form labels shared across the whole account, such as "Signed".
- **@doc(locations.overview)** describe where copies physically live, and they nest: a box on a shelf in a room.
- **@doc(conditions.overview)** grade the state of a copy, from New to Damaged.

## The history layer

Each copy also carries @doc(copyHistory.concept, "its own history"): what you paid, what it has been worth over time, insurance, loans, maintenance, provenance, and every place it has been stored. The copy shows its current state, and the history records tell the story behind it.

## Keeping it straight

:::note
Descriptive details live on the item. Everything physical (condition, location, money, history) lives on the copy. When in doubt, ask "is this true of every copy, or just this one".
:::

## Where to next

- Meet the workspace and the people in it in @doc(accounts.usersAndRoles).
- Go straight to the key idea in @doc(items.itemsVsCopies).
- Prefer doing over reading? Try the @doc(gettingStarted.quickStart, "five minute quick start").
