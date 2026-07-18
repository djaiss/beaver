## General

Do not tell me I am right all the time. Be critical. We're equals. Try to be neutral and objective.

Do not excessively use emojis.

## Coding instructions

- Write code as simply as possible - do not over-engineer so anyone can understand it.
- Always follow the Laravel best practices and how we structure our codebase.
- If you are unsure about a specific implementation, ask for clarification before proceeding.
- When you write tests, read the testing rules in `rules/testing.md` and follow them.
- When writing code, follow the coding standards in `rules/code-style.md`.

## Tech Stack & Architecture

- Backend: PHP 8.4+ / latest version of Laravel
- Frontend: Blade / Tailwind CSS / Alpine Ajax / Alpine.js
- Data is encrypted at rest in the database using Laravel's built-in encryption.
- Stricly follow PHP guidelines in ./php-guidelines.md.

## Application structure

- `app/Actions`: one class per user action, holding the business logic. Controllers stay thin and delegate here. Most of the app lives in this folder.
- `app/Models`: Eloquent models. `app/Models/Concerns` holds shared model traits, such as `HasAuthor`.
- `app/Http/Controllers`: split into `App` (the logged in app), `Api` (the JSON API) and `Marketing` (the public site and docs).
- `app/Http/Middleware`: route middleware, including the role gates. `app/Http/Resources`: API transformers.
- `app/Jobs`: queued jobs. `app/Mail`: mailables. `app/Enums`: enums. `app/Helpers`: helpers.
- `app/Services`: `ApiDocumentation` builds the API reference served at `/docs` from the endpoint definition files in `resources/docs/api`.
- `app/View/Components`: the layout components (app, guest, marketing).
- `resources/views`: `app` for the logged in screens, `components` for shared UI, `layouts` and `partials` for the shell, `marketing` for the public site, `mail` for emails.
- `resources/css` and `resources/js`: the Tailwind theme and the Alpine setup.
- `routes`: `web.php` (logged in), `auth.php`, `api.php`, `marketing.php`, `console.php`.
- `database`: `migrations`, `factories`, `seeders`, plus `data` for seed files such as countries.
- `lang`: one JSON file per locale.
- `tests`: `Unit` (models, actions, jobs), `Feature` (controllers), `Browser` (Pest browser tests).

## Models

An `Account` is the tenant. Everything in the collection domain belongs to exactly one account and is role gated (owners and editors write, viewers read).

- `Account`: the tenant. Owns the users, collections, types, locations and invitations, and answers the role questions (`allowsManagementBy()`).
- `User`: an authenticated person. Belongs to one account, and carries its `role` (owner, editor, viewer) as a column.
- `Invitation`: a pending invite to join an account at a given role, claimed with a token.
- `Collection`: a set of items being catalogued, such as "My Comics". Has a public uuid and a visibility (private, shared, public).
- `CollectionType`: a user defined category (Comics, Vinyl, Wine) that decides which custom fields apply. It lives in the `types` table, so its foreign keys are `type_id`.
- `CustomField`: a field definition on a type (Issue #, Vintage). Reaches its account through the type.
- `Location`: where an item is physically stored. Nests into itself through `parent_id`.
- `Log`: the audit trail of user actions, and the source of the dashboard activity feed.
- `EmailSent`: a record of every email the app sent, with delivery tracking.
- `WebhookEndpoint`: a webhook destination. Scoped to a user rather than an account.
- `UserDeletionReason`: why someone deleted their user. Left unlinked on purpose so it outlives them.
- `Country`: a read only lookup of countries, seeded from `database/data`.

There is no `Item` model yet. Collections, types, custom fields and locations are in place, but the item they describe is still to be built.

## Docker and self-hosting

Self-hosting Beaver with Docker is a first-class, supported use case. The setup lives in `Dockerfile` (a multi-stage build), `docker-compose.yml`, the `docker/` directory (php, nginx, supervisor and entrypoint config), `.env.docker.example` and `docker/README.md`. The same image runs three roles selected by `CONTAINER_ROLE`: web (`app`), queue worker (`queue`) and scheduler (`scheduler`).

Every feature MUST keep the Docker image building and running. When your change touches any of the following, update the Docker setup in the same PR:

- A new PHP extension requirement: add it to the `install-php-extensions` list in the `Dockerfile`.
- A new or renamed environment variable: add it to `.env.docker.example` (and document it in `docker/README.md` if an operator must set it).
- A new queue name: add it to the `queue:work --queue=...` command in `docker-compose.yml`.
- A new long-running process, scheduled task, or external service dependency: wire it into `docker-compose.yml` and the entrypoint.
- A new front-end or Composer dependency, or a change to the build steps: make sure the `assets` and `vendor` build stages still produce a working image.

Migrations MUST be upgrade-safe so that pulling a newer image never breaks or wipes an existing database. The entrypoint only ever runs `php artisan migrate --force` (pending migrations only). Never rely on `migrate:fresh` or `migrate:refresh` in the image, and avoid destructive migrations (dropping or renaming a populated column, for example) unless you provide a safe, multi-step path that preserves existing data. The database and uploaded files live in named volumes that are independent of the image.

Note that the entrypoint skips `route:cache` on purpose, because a closure route exists in `routes/web.php`. It still caches config, events and views. If you add a route that cannot be serialized, do not re-enable route caching.

After a change that could affect any of this, verify that `docker compose build` succeeds, `docker compose up` starts cleanly, and the `/up` health endpoint reports healthy.

## Guidelines for git and Github

- You MUST create a new branch when doing a new task, unless stated otherwise, based off of main branch. Make sure main is always up-to-date.
- Branche names MUST be of the format YYYYMMDD-{name}.
- You MUST follow conventional commits for commit messages.
- NEVER mention Claude Code in PR descriptions, PR comments, or issue comments.

## Writing something

Never use dashes (— or -) as punctuation in documentation or README files. Rephrase sentences using periods, commas, or parentheses instead.
