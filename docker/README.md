# Self-hosting Beaver with Docker

Beaver ships a production Docker image and a Compose stack so you can run your
own instance. Self-hosting is a first-class, supported use case.

## What the stack runs

| Service     | Role                                              |
| ----------- | ------------------------------------------------- |
| `app`       | nginx + PHP-FPM web server. Runs migrations on boot. |
| `queue`     | Queue worker (processes the `high`, `default`, `low` queues). |
| `scheduler` | Runs the artisan schedule (e.g. the daily inactive-user cleanup). |
| `mysql`     | MySQL 8.4 database.                               |

The `app`, `queue` and `scheduler` services all run the same image; only the
role differs. Sessions, cache and the queue are database-backed by default, so
no Redis or extra service is required.

## Requirements

- Docker Engine 24+ and the Compose plugin (`docker compose`).

## Quick start

```bash
cp .env.docker.example .env

# Generate a unique application key and paste it into .env as APP_KEY.
docker compose run --rm app php artisan key:generate --show

# Review the database passwords and APP_URL in .env, then start everything.
docker compose up -d --build
```

Beaver is then available at the `APP_URL` you set (http://localhost:8000 by
default). Create your account from the registration page.

## Configuration

All configuration lives in `.env` (copied from `.env.docker.example`). The
values that matter most:

- `APP_KEY`: required, unique, and shared by every container. Never change it
  on a running instance, or existing sessions and encrypted data become
  unreadable.
- `APP_URL`: the public URL of your instance.
- `APP_PORT`: the host port the web container is published on.
- `DB_PASSWORD` and `DB_ROOT_PASSWORD`: set real secrets before first boot.
- `MAIL_*`: configure SMTP or Resend to send real email (defaults to the log).

## Data and persistence

Two named volumes hold all state, independent of the image:

- `db-data`: the MySQL database.
- `storage-data`: uploaded item photos and application logs
  (`/var/www/html/storage`).

Because the data lives in volumes, replacing or upgrading the image never
touches it.

## Upgrading

Upgrades are designed to be safe for an existing database:

```bash
git pull                       # or pull a new tagged image
docker compose up -d --build
```

On boot the `app` container runs `php artisan migrate --force`, which applies
**only new, pending migrations**. It never runs `migrate:fresh` or
`migrate:refresh`, so your data is preserved. To manage migrations yourself,
set `RUN_MIGRATIONS=false` and run `docker compose exec app php artisan migrate
--force` when you choose.

### One-off step after upgrading to the photos screen

The photos screen searches encrypted file names through an index the app keeps
beside them, and shows the pixel size of each image. Neither exists for photos
uploaded before that release, so the screen finds nothing until the index is
built once:

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

It reads every photo off the disk, so give it a moment on a large library. It is
safe to run again at any time, and photos uploaded from then on are indexed as
they arrive.

## Administering the instance

The instance administration lives at `/instance-admin` and lists every account
and user on the instance. It is gated on a per user flag that nobody has by
default, so grant it to yourself once after registering your user:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Pass `--revoke` to take it away again. The flag is separate from the owner,
editor and viewer roles, which only ever apply inside a single account.

## Backups

```bash
# Database
docker compose exec mysql \
  mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > beaver-backup.sql

# Uploaded files
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine \
  tar czf /backup/beaver-storage.tar.gz -C /data .
```

## Common commands

```bash
docker compose logs -f app                 # tail the web logs
docker compose exec app php artisan tinker # a REPL inside the app
docker compose ps                          # service status and health
docker compose down                        # stop (keeps the volumes)
docker compose down -v                     # stop and DELETE all data
```

## Troubleshooting

- **`APP_KEY is not set`.** Run the `key:generate --show` step above and paste
  the value into `.env`.
- **Web container is unhealthy.** Check `docker compose logs app`; it usually
  means the database was not reachable or a migration failed.
- **Uploads fail.** Ensure the `storage-data` volume is mounted and writable
  (the entrypoint fixes ownership automatically on boot).
