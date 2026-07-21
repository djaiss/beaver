---
id: tutorials.selfHostWithDocker
title: "Tutorial: Self host KolleK with Docker"
slug: self-host-with-docker
section: tutorials
---

# Tutorial: Self host KolleK with Docker

In this tutorial you will take a machine with nothing on it to a running KolleK instance: clone the project, configure the environment, generate the application key, start the stack, create the first account, and grant the first instance administrator. At the end you will have a working instance and know where the deeper operational guides pick up.

We will follow Alex, who is setting up an instance for their collectors club on a small home server. The steps are identical on a VPS or a laptop.

Expect this to take fifteen to thirty minutes, most of it waiting for the first build.

## Before you start

You need:

- A machine with **Docker Engine 24 or newer** and the **Compose plugin** (the `docker compose` command, not the older `docker-compose`).
- **Git**, to clone the project.
- A terminal and basic comfort running commands on it.

It also helps to skim the @doc(selfHosting.index, "self hosting overview") first, because it introduces the one rule this tutorial will insist on: the application key is set once and never changed.

## Step 1: Clone the project and create your configuration

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

The `.env` file is your instance's configuration. Everything an operator routinely touches lives in it, and the @doc(selfHosting.configure, "configuration guide") walks through it group by group. For a first boot, only the next two steps are mandatory.

## Step 2: Generate the application key

KolleK encrypts sensitive data at rest with a key that you generate once:

```bash
docker compose run --rm app php artisan key:generate --show
```

Copy the output (it starts with `base64:`) and paste it into `.env` as the value of `APP_KEY`.

:::warning
Set the application key once and never change it on a running instance. Everything encrypted, which includes names, items, and sessions, becomes permanently unreadable under a different key. Store a copy of the key somewhere safe, because a database backup is only restorable with the key that encrypted it.
:::

The full story, including how deliberate key rotation is supported, is in @doc(selfHosting.applicationKeyAndEncryption).

## Step 3: Review passwords and the URL

Open `.env` in an editor and check three things:

- **`DB_PASSWORD` and `DB_ROOT_PASSWORD`.** Both ship as placeholder values. Change them to strong passwords of your own before the first start, because the first start is when the database is created with them.
- **`APP_URL`.** The address your users will type. Alex sets `http://server.local:8000` for the club's network. The default is `http://localhost:8000`.
- **`APP_PORT`.** The published port, `8000` unless you change it.

## Step 4: Start the stack

```bash
docker compose up -d --build
```

The first run builds the image and takes a few minutes. Compose then starts four containers:

- **app**, the web server. This is the only role that runs database migrations, so the schema is set up exactly once.
- **queue**, the background worker that sends emails and processes jobs.
- **scheduler**, which runs the daily maintenance jobs.
- **mysql**, the database.

Check that everything is up with `docker compose ps`. When the app container reports healthy, open your `APP_URL` in a browser. You should see KolleK's sign in screen.

## Step 5: Create the first account

Go to the registration page and sign up. This works exactly as it does for any user, the walkthrough is in @doc(accounts.create), and it makes you the owner of the instance's first account.

Alex registers, lands on the getting started checklist, and resists cataloguing anything until the operator work is finished.

## Step 6: Grant the first instance administrator

An instance administrator can see across every account on the instance, from the instance administration panel. The flag is granted from the command line:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Use the email you just registered with. The same command with `--revoke` takes the flag back. What the flag does, and deliberately does not do, is covered in @doc(instanceAdmin.grantAccess).

## The result

You have a working instance: the web app answering on your URL, a queue worker and scheduler running beside it, data in a named database volume, and yourself as both an account owner and the instance administrator. Club members can now register their own accounts, or you can @doc(tutorials.inviteHousehold, "invite people into yours").

## One thing to do before you relax

Out of the box, the instance only writes outgoing email to a log file instead of sending it. Invitations, magic links, and password resets will silently go nowhere until you configure a real mailer. That is deliberate, and fixing it is a short job: @doc(selfHosting.setupEmailDelivery).

## Common mistakes to avoid

- **Losing the application key.** Back it up now, separately from the database. Without it, backups are ciphertext.
- **Leaving the placeholder database passwords.** Change them before first start, not after.
- **Skipping email setup.** The first "my invitation never arrived" report will be this.

## Where to next

- Walk through every setting you skipped in @doc(selfHosting.configure).
- Set up @doc(selfHosting.backupAndRestore, "backups") before the catalogue grows precious.
- When a new version ships, follow @doc(selfHosting.upgrade).
