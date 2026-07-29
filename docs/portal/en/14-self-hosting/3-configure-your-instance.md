---
id: selfHosting.configure
title: Configure your instance
slug: configure-your-instance
section: self-hosting
---

# Configure your instance

Everything about your instance is configured through the `.env` file you created during @doc(selfHosting.installDocker, "installation"). This page walks through the settings an operator actually touches, grouped by what they do, rather than listing every variable the template contains.

After changing `.env`, apply it by recreating the containers:

```bash
docker compose up -d
```

## Identity and URL

- `APP_NAME` is the name shown in the interface and in emails. It defaults to `Kollek`.
- `APP_URL` is the public address of your instance. Links in emails are built from it, so it must be the address your users really use.
- `APP_PORT` is the host port the web container publishes, `8000` by default.

## The application key

`APP_KEY` encrypts sensitive data at rest. You set it once during installation and never change it casually. It is important enough to have @doc(selfHosting.applicationKeyAndEncryption, "its own page"), which also covers the `APP_PREVIOUS_KEYS` rotation mechanism.

## Database

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_ROOT_PASSWORD` configure the bundled MySQL container. Change both passwords from their placeholders before first boot. `RUN_MIGRATIONS` controls whether the web container migrates on boot (`true` by default).

## Email

`MAIL_MAILER` decides how email leaves your instance, and it defaults to `log`.

:::note
With the default `log` mailer, no email is ever sent. Invitations, magic links, password resets, and security alerts are written to the application log instead. Configuring a real mailer is the one piece of setup nearly every instance needs. See @doc(selfHosting.setupEmailDelivery).
:::

## File storage

`FILESYSTEM_DISK` is `local` by default: uploaded photos and documents are stored in the `storage-data` volume. To use S3 compatible object storage instead, set it to `s3` and fill in the `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, and, for non AWS providers, `AWS_ENDPOINT` variables. Files are served to users through private, account checked routes either way, never as public URLs.

## Housekeeping

- `TRASH_RETENTION_DAYS` is how long soft deleted objects stay in the @doc(dataSafety.restoreFromTrash, "trash") before the nightly purge removes them for good. The default is 30 days.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` is the address notified when a user deletes their own user or is removed by the @doc(users.inactiveDeletion, "inactivity cleanup"). Point it at yourself so departures do not go unnoticed.

## The public marketing site

`SHOW_MARKETING_SITE` is `false` by default, meaning your instance serves only the application itself. Set it to `true` to also serve the public marketing pages and the generated API reference at `/docs/api`. Most private instances leave it off; turn it on if your developers want the API reference served locally.

Those pages are the same for every visitor and change only when you redeploy, so each one is sent with cache headers: a browser holds it for five minutes, and any CDN in front of the instance holds it for a week. Set `CACHE_PUBLIC_PAGES` to `false` to stop sending them. Nothing is cached on the instance itself either way.

If you serve the site through Cloudflare, set `CLOUDFLARE_API_TOKEN` and `CLOUDFLARE_ZONE_ID` as well. They are what lets the instance tell Cloudflare to drop what it holds when something the public site shows changes, either by itself or through @doc(instanceAdmin.panel, "the panel"). Leave them empty on an instance served directly: there is nothing in front of it to purge.

Either way, the instance answers `/robots.txt` itself rather than serving a file from disk. With the public site on, it points crawlers at `/sitemap.xml`, which lists every public page in every language it is offered in. With the public site off, it tells every crawler to stay out of the whole host, because a private instance has nothing worth indexing.

## Spam protection

Nothing stops a bot from filling in your sign up form, and on an instance anybody can reach that happens sooner or later. `TURNSTILE_ENABLED` puts a Cloudflare Turnstile widget on the sign in, sign up and password reset forms, so a submission that has not solved it never reaches the application.

It is `false` by default, which is the right answer for a private instance where you know everyone who signs in. To turn it on, create a Turnstile widget in your Cloudflare account, then set `TURNSTILE_ENABLED` to `true`, `TURNSTILE_SITE_KEY` to the public key it gives you and `TURNSTILE_SECRET_KEY` to the private one. The flag on its own protects nothing: without both keys, no visitor can get past the check.

:::note
With the widget on, your instance verifies every submission with Cloudflare, so it has to reach `challenges.cloudflare.com` for anybody to sign in. A verification that cannot happen counts as a verification that failed, deliberately. An instance without dependable outbound internet access should leave this off.
:::

## What you do not need to configure

Sessions (`SESSION_DRIVER`), cache (`CACHE_STORE`), and the queue (`QUEUE_CONNECTION`) are all `database` backed out of the box. The defaults are correct for the provided stack, and there is no Redis or other service to add. Leave them alone unless you know precisely why you are changing them.

## Where to next

- Get real email flowing in @doc(selfHosting.setupEmailDelivery).
- Understand the key you must protect in @doc(selfHosting.applicationKeyAndEncryption).
- Put @doc(selfHosting.backupAndRestore, "backups") in place.
