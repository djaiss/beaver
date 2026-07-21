# Configure your instance

Everything about your instance is configured through the `.env` file you created during [installation](install-with-docker.md). This page walks through the settings an operator actually touches, grouped by what they do, rather than listing every variable the template contains.

After changing `.env`, apply it by recreating the containers:

```bash
docker compose up -d
```

## Identity and URL

- `APP_NAME` is the name shown in the interface and in emails. It defaults to `Kollek`.
- `APP_URL` is the public address of your instance. Links in emails are built from it, so it must be the address your users really use.
- `APP_PORT` is the host port the web container publishes, `8000` by default.

## The application key

`APP_KEY` encrypts sensitive data at rest. You set it once during installation and never change it casually. It is important enough to have [its own page](application-key-and-encryption.md), which also covers the `APP_PREVIOUS_KEYS` rotation mechanism.

## Database

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `DB_ROOT_PASSWORD` configure the bundled MySQL container. Change both passwords from their placeholders before first boot. `RUN_MIGRATIONS` controls whether the web container migrates on boot (`true` by default).

## Email

`MAIL_MAILER` decides how email leaves your instance, and it defaults to `log`.

:::note
With the default `log` mailer, no email is ever sent. Invitations, magic links, password resets, and security alerts are written to the application log instead. Configuring a real mailer is the one piece of setup nearly every instance needs. See [Set up email delivery](set-up-email-delivery.md).
:::

## File storage

`FILESYSTEM_DISK` is `local` by default: uploaded photos and documents are stored in the `storage-data` volume. To use S3 compatible object storage instead, set it to `s3` and fill in the `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, and, for non AWS providers, `AWS_ENDPOINT` variables. Files are served to users through private, account checked routes either way, never as public URLs.

## Housekeeping

- `TRASH_RETENTION_DAYS` is how long soft deleted objects stay in the [trash](../data-safety/restore-from-trash.md) before the nightly purge removes them for good. The default is 30 days.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` is the address notified when a user deletes their own user or is removed by the [inactivity cleanup](../data-safety/inactive-user-deletion.md). Point it at yourself so departures do not go unnoticed.

## The public marketing site

`SHOW_MARKETING_SITE` is `false` by default, meaning your instance serves only the application itself. Set it to `true` to also serve the public marketing pages and the generated API reference at `/docs/api`. Most private instances leave it off; turn it on if your developers want the API reference served locally.

## What you do not need to configure

Sessions (`SESSION_DRIVER`), cache (`CACHE_STORE`), and the queue (`QUEUE_CONNECTION`) are all `database` backed out of the box. The defaults are correct for the provided stack, and there is no Redis or other service to add. Leave them alone unless you know precisely why you are changing them.

## Where to next

- Get real email flowing in [Set up email delivery](set-up-email-delivery.md).
- Understand the key you must protect in [The application key and encryption](application-key-and-encryption.md).
- Put [backups](back-up-and-restore.md) in place.
