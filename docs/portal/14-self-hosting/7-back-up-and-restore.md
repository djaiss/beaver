---
id: selfHosting.backupAndRestore
title: Back up and restore your instance
slug: back-up-and-restore
section: self-hosting
---

# Back up and restore your instance

There is no automated backup inside KolleK. Protecting the data is the operator's job, and this page is the procedure. It is also, today, the real answer to "how do I export everything", as [Back up your collection data](../11-data-safety/6-back-up-your-collection-data.md) explains from the collector's side.

## What a complete backup is

Three things, and all three matter:

1. **The database**, in the `db-data` volume. Every record: accounts, collections, items, copies, history.
2. **The storage volume**, `storage-data`. Every uploaded photo and document.
3. **The application key**, `APP_KEY` from your `.env` (plus `APP_PREVIOUS_KEYS` if set).

:::warning
A backup without its matching application key is not a backup. Encrypted fields restore as unreadable ciphertext without the key that wrote them. Store the key with, or alongside, every backup you take. See [The application key and encryption](5-application-key-and-encryption.md).
:::

## Back up

Dump the database:

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

Archive the storage volume:

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

Copy both files, and a copy of your `.env`, somewhere off the server. Automate this with a nightly cron job and keep more than one generation; a backup you have never restored from is a hope, not a plan.

## Restore

On a fresh machine, restore in this order:

1. Install the same KolleK version following [Install with Docker](2-install-with-docker.md), but set `APP_KEY` (and `APP_PREVIOUS_KEYS`) from your backup instead of generating a new key.
2. Start the stack once so the volumes exist, then load the database dump:

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. Unpack the storage archive into the storage volume:

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. Restart the stack with `docker compose up -d` and sign in to verify.

## The command that deletes everything

:::warning
`docker compose down -v` removes the named volumes, which is the database and every uploaded file. Never use the `-v` flag on a real instance. Plain `docker compose down` is safe and leaves the volumes intact.
:::

## Where to next

- Understand what the key protects in [The application key and encryption](5-application-key-and-encryption.md).
- See what collectors can export from inside the app in [Back up your collection data](../11-data-safety/6-back-up-your-collection-data.md).
