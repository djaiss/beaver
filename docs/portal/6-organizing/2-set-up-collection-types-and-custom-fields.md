---
id: collectionTypes.setup
title: Set up collection types and custom fields
slug: set-up-collection-types-and-custom-fields
section: organizing
---

# Set up collection types and custom fields

A [collection type](../3-core-concepts/6-collection-types-and-custom-fields.md) decides which details an item can record. A comic wants an issue number and a publisher. A vinyl record wants an artist and a pressing. This page shows you how to create a type, add custom fields to it, and keep long forms readable with field groups.

You need the editor or owner role to manage types. Types are account wide, so a type you set up carefully once can be reused by any number of collections.

## Start from the ready made types

A fresh account already includes a dozen ready made types (Comics, Trading Cards, Vinyl Records, CD, DVD, Coins, Stamps, Books, Action Figures / Toys, Video Games, Watches, and Wine), each with sensible fields already grouped. Before building from scratch, open the one closest to your hobby and adjust it. Renaming a field or adding one is faster than starting empty.

## Create a type

Noah collects vinyl and wants a type for concert posters, something the defaults do not cover.

::::steps
:::step title="Open collection types"
Go to your account settings and open **Collection types**.

::screenshot{label="Collection types list in account settings"}
:::

:::step title="Create the type"
Choose **New type**, give it a name (Noah types "Concert Posters"), and pick a color. The color helps you tell types apart at a glance in lists.
:::

:::step title="Add your first fields"
Open the new type and add a custom field for each detail you want to record. For each field, choose a name and a field type.

::screenshot{label="Type editor with the field list"}
:::
::::

The type editor saves as you go. There is no separate save button to remember; each change is stored the moment you make it.

## Choose the right field type

Each custom field has one of six field types:

- **Text** for free form details, such as an artist or a venue.
- **Number** for quantities and measurements, such as an issue number or a print run.
- **Date** for anything calendar based, such as a concert date.
- **Yes / No** for simple flags, such as "Signed" or "First edition".
- **Select** for a fixed list of options you define, such as a publisher or a grade. Options keep data consistent, because everyone picks from the same list instead of typing variations.
- **Rating** for a personal score of one to five stars.

Prefer **Select** over **Text** whenever the possible values are a known list. "Marvel" and "marvel comics" look the same to you but not to a filter.

## Keep forms readable with field groups

Fields can be organized into named groups, and each group renders as its own section on the item form. The ready made Comics type, for example, groups its fields into "Publishing info" and "Condition & grading". Ungrouped fields appear first.

Create a group, give it a name, and move fields into it. You can reorder both the fields inside a group and the groups themselves, so the form reads in the order that makes sense for your hobby.

:::note
Groups only affect how the item form is presented. They change nothing about the data itself, so feel free to reorganize at any time.
:::

## Attach the type to collections

A type does nothing until a [collection](../3-core-concepts/4-collections.md) enables it. When you create or edit a collection, choose which types apply. A collection can enable several, and the same type can serve many collections. Once enabled, items in that collection can pick the type and fill in its fields.

## Where to next

- Share a setup you are proud of, or borrow one, with [Import and export a collection type](3-import-and-export-a-collection-type.md).
- Put the fields to work in [Add and edit items](../4-core-features/4-add-and-edit-items.md).
- Round out your setup with [Set up your locations](7-set-up-your-locations.md).
