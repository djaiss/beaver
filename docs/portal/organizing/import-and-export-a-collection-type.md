# Import and export a collection type

A carefully built [collection type](../core-concepts/collection-types-and-custom-fields.md) is worth sharing. KolleK can export a type definition as a JSON file and import one back in, so you can copy a setup between accounts, share it with another collector, or keep a snapshot before reworking it.

You need the editor or owner role.

## What moves, and what does not

The export contains the type definition only: its name, its color, its field groups, its custom fields, and the options of any select fields.

:::note
Exporting a type does not export items or their data. There is currently no item or whole collection import or export. See the [feature status page](../troubleshooting/feature-status.md) for where that stands, and [Back up your collection data](../data-safety/back-up-your-collection-data.md) for what portability exists today.
:::

## Export a type

::::steps
:::step title="Open the type"
In account settings, open **Collection types** and select the type you want to export.
:::

:::step title="Export it"
Choose **Export**. KolleK downloads a JSON file describing the type.

::screenshot{label="Type editor with the export option"}
:::
::::

The file is plain text. You can read it, keep it with your backups, or send it to someone.

## Import a type

Importing works from pasted JSON, so first open the file you received in any text editor and copy its contents.

::::steps
:::step title="Start the import"
In account settings, open **Collection types** and choose **Import**.
:::

:::step title="Paste the JSON"
Paste the type definition into the field and confirm. KolleK validates it and creates the type with its groups, fields, and options.

::screenshot{label="Import form with pasted JSON"}
:::

:::step title="Review the result"
Open the new type and check the fields arrived as expected, then attach it to a collection to start using it.
:::
::::

## A worked example

Noah's friend also collects vinyl and has refined a "Vinyl Records" type with a grouped set of fields: release info (artist, album, release year) and pressing details (pressing, speed, colored vinyl). Rather than rebuilding it by hand, Noah asks for the export, pastes the JSON into his own account, and has the identical structure in seconds.

If you want to see the exact format the importer expects, export any existing type first, such as the ready made Comics type, and use it as a template. Your own exports always import back cleanly.

## Where to next

- Refine the imported type in [Set up collection types and custom fields](set-up-collection-types-and-custom-fields.md).
- Understand what else can and cannot be exported in [Back up your collection data](../data-safety/back-up-your-collection-data.md).
