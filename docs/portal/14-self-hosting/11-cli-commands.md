---
id: selfHosting.cliCommands
title: Administer with the command line
slug: cli-commands
section: self-hosting
---

# Administer with the command line

A few operator tasks live on the command line rather than in the web app. This page lists the artisan commands you may actually need while running an instance, with a pointer to the fuller page for each.

On a Docker install, run every command through the web container:

```
docker compose exec app php artisan <command>
```

## Day to day operation

### Grant or revoke instance administration

```
php artisan beaver:make-instance-administrator you@example.com
php artisan beaver:make-instance-administrator you@example.com --revoke
```

Grants (or takes back) the server wide administrator flag for the user with that email. This is how the first administrator is bootstrapped after installation. See @doc(instanceAdmin.grantAccess).

### Create a webhook endpoint

```
php artisan beaver:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Registers a webhook endpoint for a user and prints its ID and signing secret. Users can also do this themselves from their profile settings. Note that no application event fires webhooks yet; see @doc(webhooks.overview).

### Rebuild the photo search index

```
php artisan photos:rebuild-search-index
```

Rebuilds the search index behind the photo library and backfills missing image dimensions. Run it once after upgrading to a version that introduces the photo screen. It is safe to run again at any time; it skips photos whose files are missing and changes nothing else. See @doc(selfHosting.upgrade).

### Scaffold a locale for translation

```
php artisan beaver:localize fr_FR
```

Extracts every translatable string in the application and syncs it into the locale's JSON file under `lang/`. See @doc(selfHosting.addLanguage).

## Development only

Two more commands exist in the codebase, and neither belongs on a production instance. `beaver:bruno` resets the database with seed data for API client testing, which would destroy real data, and `beaver:sync-skills` maintains the project's own tooling. You can ignore both as an operator.

:::warning
Never run `beaver:bruno` on a real instance. It wipes the database and reseeds it with demo data.
:::

## Where to next

- Bootstrap your administrator in @doc(instanceAdmin.grantAccess).
- Keep the instance current with @doc(selfHosting.upgrade).
- Translate the interface in @doc(selfHosting.addLanguage).
