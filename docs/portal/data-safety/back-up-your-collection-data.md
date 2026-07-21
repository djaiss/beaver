# Back up your collection data

"How do I get my data out" deserves a straight answer. This page states plainly what KolleK can export from within the app today, what it cannot yet, and what the real backup path is in the meantime.

## What you can export today

**Collection type definitions.** A [collection type](../core-concepts/collection-types-and-custom-fields.md) can be exported as a JSON file (its name, color, field groups, fields, and options) and imported into any KolleK account. See [Import and export a collection type](../organizing/import-and-export-a-collection-type.md).

That is the honest, complete list.

## What you cannot export yet

There is currently no built in export of items, copies, photos, or whole collections, and no corresponding import. Your catalogue data cannot yet be pulled out of the app as a file from the interface.

:::note
Item and collection import and export are on the list of planned capabilities. The [feature status page](../troubleshooting/feature-status.md) is the maintained record of where this stands, so check there rather than assuming.
:::

If you need structured access to your data today, the [JSON API](../developers/api-overview.md) can read everything in your account, which is a workable path for the technically inclined.

## The real backup path today

If your instance is self hosted, the dependable backup is taken at the instance level: a database dump plus an archive of the storage volume that holds photos and documents. That captures absolutely everything, including what the in app export cannot reach. The walkthrough lives in [Back up and restore your instance](../self-hosting/back-up-and-restore.md).

If someone else hosts KolleK for you, they hold that backup ability. Ask them what their backup arrangements are; it is a fair and important question.

## Where to next

- Self hosting? Set up real backups in [Back up and restore your instance](../self-hosting/back-up-and-restore.md).
- Moving a type setup between accounts is covered in [Import and export a collection type](../organizing/import-and-export-a-collection-type.md).
- See what else is planned on the [feature status page](../troubleshooting/feature-status.md).
