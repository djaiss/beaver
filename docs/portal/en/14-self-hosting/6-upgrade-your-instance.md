---
id: selfHosting.upgrade
title: Upgrade your instance
slug: upgrade-your-instance
section: self-hosting
---

# Upgrade your instance

Upgrading KolleK is designed to be boring: pull the newer version, rebuild, done. This page explains why that is safe, and the one post upgrade step to know about.

## Why upgrades do not lose data

Two properties make the upgrade path safe:

- **Your data lives in named volumes** (`db-data` for the database, `storage-data` for files), independent of the containers and the image. Rebuilding containers does not touch them.
- **Migrations are forward only.** The web container applies pending database migrations on boot with `migrate --force`, and KolleK never ships a migration that resets or destructively rewrites data. An upgrade only ever adds to your schema.

## Upgrade

::::steps
:::step title="Back up first"
Take a database dump and a storage archive as described in @doc(selfHosting.backupAndRestore). Upgrades are safe by design, but a backup turns "safe by design" into "safe, full stop".
:::

:::step title="Get the new version"
From the repository directory, pull the release you are upgrading to:

```bash
git pull
```
:::

:::step title="Rebuild and restart"
```bash
docker compose up -d --build
```

Compose rebuilds the image and recreates the containers. On boot, the web container applies any new migrations automatically, then the instance is back at your `APP_URL`.
:::
::::

If you prefer migrations under manual control, set `RUN_MIGRATIONS=false` and run `docker compose exec app php artisan migrate --force` yourself as part of the procedure, as covered in @doc(selfHosting.installDocker).

## The photo search index step

One upgrade includes a one time maintenance task: instances that predate the photo library screen need their photo search index built once, or photo search stays empty for existing photos.

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

The command is idempotent and safe to run on any instance, so when in doubt, run it. It also backfills image dimensions for photos uploaded before dimensions were recorded.

:::note
Do not change `APP_KEY` as part of an upgrade. The key outlives every version. If an upgrade guide ever seems to ask for a new key, you are misreading it. See @doc(selfHosting.applicationKeyAndEncryption).
:::

## Where to next

- Keep @doc(selfHosting.backupAndRestore, "backups") current so every upgrade starts from one.
- Review @doc(selfHosting.scheduledJobs), which resume automatically once the scheduler container is back up.
