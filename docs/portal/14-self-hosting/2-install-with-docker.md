---
id: selfHosting.installDocker
title: Install with Docker
slug: install-with-docker
section: self-hosting
---

# Install with Docker

This is the authoritative installation guide. It takes you from a machine with Docker to a running KolleK instance with your first account created. Expect the whole thing to take around fifteen minutes.

The repository's `docker/README.md` documents the same procedure from the operator's side and is kept in sync with the code. If this page and that file ever disagree, trust `docker/README.md`.

## Before you start

You need:

- A machine with **Docker Engine 24 or newer** and the **Compose plugin** (`docker compose`).
- A copy of the KolleK repository, cloned or downloaded.
- Ten minutes of attention for the environment file. It is where the mistakes that matter happen.

Nothing else. The stack brings its own MySQL database, and sessions, cache, and the queue are database backed, so there is no Redis to install.

## Install

::::steps
:::step title="Create your environment file"
From the repository root, copy the Docker environment template:

```bash
cp .env.docker.example .env
```

This file drives the whole stack. You will edit it in the next two steps.
:::

:::step title="Generate the application key"
Generate a key and copy the output:

```bash
docker compose run --rm app php artisan key:generate --show
```

Paste the printed value into `.env` as `APP_KEY`. This key encrypts your data at rest. **Set it now and never change it later.** A changed key makes every encrypted field and every session permanently unreadable. Read @doc(selfHosting.applicationKeyAndEncryption) before going further if you have not yet.
:::

:::step title="Review passwords and the URL"
In `.env`, change `DB_PASSWORD` and `DB_ROOT_PASSWORD` from their placeholder values, and set `APP_URL` to the address your users will visit. The default is `http://localhost:8000`, which is fine for a first try on your own machine.
:::

:::step title="Start the stack"
Build and start everything:

```bash
docker compose up -d --build
```

The first build takes a few minutes. When it finishes, the web container applies the database migrations automatically and the instance comes up at your `APP_URL`.
:::

:::step title="Create your first account"
Open the URL in a browser and use the registration page to sign up. This creates your personal user and your first account, exactly as described in @doc(accounts.create).

::screenshot{label="Registration page of a freshly installed instance"}
:::

:::step title="Grant yourself instance administrator access"
If you want the server wide administration panel, grant your user the flag:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

See @doc(instanceAdmin.grantAccess) for what this does and does not give you.
:::
::::

## What is actually running

The Compose stack starts four containers. Three of them run the same KolleK image in different roles, chosen by the `CONTAINER_ROLE` environment variable:

- **app** serves the web application through nginx and PHP. It is the only container that runs database migrations, and it does so on boot.
- **queue** processes background jobs (email, deliveries, logging) from the `high`, `default`, and `low` queues.
- **scheduler** triggers the daily maintenance jobs described in @doc(selfHosting.scheduledJobs).

The fourth container is **mysql**, running MySQL 8.4.

Your data lives in two named Docker volumes, independent of the containers: `db-data` for the database and `storage-data` for uploaded photos and documents. Containers can be rebuilt and replaced freely; the volumes persist.

:::note
All three application containers must share the same `.env`, and above all the same `APP_KEY`. The Compose file already arranges this. Keep it that way if you customize the setup.
:::

## If you prefer to run migrations yourself

By default the web container migrates the database every time it boots, which keeps upgrades hands off. If you want manual control, set `RUN_MIGRATIONS=false` in `.env`, then run migrations yourself when needed:

```bash
docker compose exec app php artisan migrate --force
```

## Where to next

- Walk through @doc(selfHosting.configure) to understand what else `.env` controls.
- Make email work in @doc(selfHosting.setupEmailDelivery). Until you do, invitations and sign in links go to a log file instead of an inbox.
- Set up @doc(selfHosting.backupAndRestore, "backups") before you put real data in.
