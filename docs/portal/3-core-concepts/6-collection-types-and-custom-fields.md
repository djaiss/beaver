---
id: collectionTypes.overview
title: Collection types and custom fields
slug: collection-types-and-custom-fields
section: core-concepts
---

# Collection types and custom fields

Comics need an issue number. Wine needs a vintage. Watches need a movement. KolleK does not guess what you collect, it lets you define it. This page explains the pieces: types, custom fields, and field groups.

## Collection types

A **collection type** describes one kind of thing you collect: Comics, Vinyl Records, Wine. It is the container for the custom fields that make sense for that kind of thing.

Types are account wide and reusable. Define a Comics type once, and any [collection](4-collections.md) in your account can enable it. A collection can enable several types at once, which suits mixed collections: Noah's "Music" collection enables both Vinyl Records and CD, so each item can be catalogued as one or the other.

When an item is given a type, its form grows the custom fields that type defines.

## Custom fields

A **custom field** is one detail a type asks for. Each field has a type of its own:

- **Text**, for anything free form, such as Publisher or Artist.
- **Number**, for Issue # or Release Year.
- **Date**, for a cover date.
- **Yes / No**, for Signed or First Edition.
- **Select**, a dropdown with options you define, such as a Grade of PSA 10, PSA 9, or Raw.
- **Rating**, up to five stars, for your personal "My Rating".

The values are recorded per item. Emma's "Amazing Spider-Man #1" has Issue # 1 and Publisher Marvel; her other comics share the same fields with their own values.

## Field groups

When a type has many fields, **field groups** keep the form readable. A group is just a named section: the ready made Comics type groups its fields under "Publishing info" and "Condition and grading". Long forms read as tidy sections instead of one endless list.

## The ready made types

A fresh account ships with a dozen ready made types so you are not starting from a blank page: Comics, Trading Cards, Vinyl Records, CD, DVD, Coins, Stamps, Books, Action Figures / Toys, Video Games, Watches, and Wine, each with sensible fields already grouped. Use them as they are, adjust them, or ignore them and build your own.

:::note
Types describe items, not copies. A field that varies per physical piece you own, such as condition or a serial number, belongs on the copy instead. See [Items versus copies](5-items-and-copies.md).
:::

## Where to next

- Build or adjust a type step by step in [Set up collection types and custom fields](../6-organizing/2-set-up-collection-types-and-custom-fields.md).
- Share a type definition with someone in [Import and export a collection type](../6-organizing/3-import-and-export-a-collection-type.md).
- See fields in action in [Add and edit items](../4-core-features/4-add-and-edit-items.md).
