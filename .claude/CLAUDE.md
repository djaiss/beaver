## General

Do not tell me I am right all the time. Be critical. We're equals. Try to be neutral and objective.

Do not excessively use emojis.

## Coding instructions

- Write code as simply as possible - do not over-engineer so anyone can understand it.
- Always follow the Laravel best practices and how we structure our codebase.
- If you are unsure about a specific implementation, ask for clarification before proceeding.
- When you write tests, read the testing rules in `rules/testing.md` and follow them.
- When writing code, follow the coding standards in `rules/code-style.md`.
- ALWAYS warn users before making a destructive action in the UI.

## Tech Stack & Architecture

- Backend: PHP 8.4+ / latest version of Laravel
- Frontend: Blade / Tailwind CSS / Alpine Ajax / Alpine.js
- Data is encrypted at rest in the database using Laravel's built-in encryption.
- Stricly follow PHP guidelines in ./php-guidelines.md.

## Application structure

- `app/Actions`: one class per user action, holding the business logic. Controllers stay thin and delegate here. Most of the app lives in this folder.
- `app/Models`: Eloquent models.
- `app/Traits`: shared traits used across the app, such as `HasAuthor` (mixed into models) or `GuardsOverlappingLoans` (mixed into actions).
- `app/Http/Controllers`: split into `App` (the logged in app), `Api` (the JSON API) and `Marketing` (the public site and docs). `app/Http/Controllers/Concerns` holds shared controller traits, such as `FindsItems`.
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

## The API

The JSON API mirrors the web app: same Actions, same rules, one endpoint for
everything the app can do. When you add a capability to the app, add it to the
API in the same PR, and document it in `resources/docs/api` (a route without
documentation fails the suite).

Authorization is enforced inside the Actions, not by route middleware. An action
checks the role itself and throws `ModelNotFoundException`, so a user who may not
do something gets a 404 rather than a 403, and cross tenant lookups look exactly
the same. The `owner` and `editor` middleware on the web routes are a UX layer on
top of that, not the gate itself. Do not read `routes/api.php` alone to decide
whether an endpoint is protected, and do not add the middleware to API routes:
that would split the rule across two places.

Read endpoints have no action to enforce anything, so anything owner only when
read (the member roster, pending invitations) gates in the controller through the
`EnsuresAccountOwner` concern.

Never hand an Eloquent model straight to a JSON response. Go through a Resource,
or project the fields explicitly, so a service returning a model does not leak a
whole row.

## Inline help and documentation

The product should explain itself. Two rules follow from that, and both ship in
the same PR as the feature they describe, never later.

Inline help on forms. When you add a form, every field that is not self evident
carries an inline help popup through the `<x-help>` component, wired in with the
field component's `helpId` prop. Genuinely obvious fields, a plain name or an
email, may skip it, but anything with a rule, a consequence, or a domain meaning
gets one. Each popup is a Markdown snippet at
`docs/help/{locale}/{page}.{section}.{field}.md`, for example
`docs/help/en/settings.general.currency.md`. A snippet is authored in English and
then translated into every language the app supports (the same set as the `lang`
files), not English alone. It carries a short blurb, an optional kicker and note,
and where a matching portal page exists a `doc:` id that becomes the "Read more"
link. The content is read by the `HelpSnippets` service.

Feature documentation. Every feature has a real page in the documentation portal
under `docs/portal`, written with the `writer` skill, in the same PR that adds the
feature. A capability a user can reach but cannot read about is not finished. The
inline help `doc:` links resolve to these portal pages, so the portal stays the
single source of truth the popups defer to.

## The instance administration

`/instance-admin` looks across every account on the instance: how many there are,
who is in them, and the ability to delete an account or a user. It is gated on the
`is_instance_administrator` flag on the user through the `instance.admin`
middleware, which answers 404 rather than 403 so the panel does not announce
itself. The three actions behind it check the flag themselves, the same way the
tenant actions check the role.

This section is deliberately the one exception to mirroring the app in the JSON
API. The API is tenant scoped, every endpoint resolves through the caller's
account, and an instance wide surface has no place in it. Add capabilities here to
the web app only, and do not add `resources/docs/api` entries for them.

Grant the flag with `php artisan beaver:make-instance-administrator {email}`
(`--revoke` takes it back). The seeded `admin@admin.com` user has it. Nobody can
revoke their own flag or delete their own user from the panel, so an instance
cannot be locked out of it.

The instance administration panel is English only and is never translated. It is
operated by whoever runs the instance, not by end users, so translating it buys
nothing. Write its own copy as plain strings rather than through `__()`, `trans()`
or `trans_choice()`, and keep its keys out of the `lang` files, so nothing here
feeds the translation workflow. This covers `/instance-admin` (the views under
`resources/views/app/instance`, the `App\Http\Controllers\App\Instance`
controllers, and the panel's block of the sidebar). Data it merely displays that
is already translated elsewhere, such as activity log descriptions, may stay as
it is: the rule is about the panel's own chrome, not about re-deriving shared
values.

## Models

An `Account` is the tenant. Everything in the collection domain belongs to exactly one account and is role gated (owners and editors write, viewers read).

- `Account`: the tenant. Owns the users, collections, types, locations and invitations, and answers the role questions (`allowsManagementBy()`).
- `User`: an authenticated person. Belongs to one account, and carries its `role` (owner, editor, viewer) as a column. The separate `is_instance_administrator` flag is orthogonal to the role: it grants nothing extra inside the user's own account, and only unlocks the instance administration described below.
- `Invitation`: a pending invite to join an account at a given role, claimed with a token.
- `Collection`: a set of items being catalogued, such as "My Comics". Has a public uuid and a visibility (private, shared, public). Owns items, categories and the per user remembered views.
- `CollectionType`: a user defined category (Comics, Vinyl, Wine) that decides which custom fields apply. It lives in the `types` table, so its foreign keys are `type_id`.
- `CustomFieldGroup`: a named section (Main, Details) that groups custom fields on a type. Reaches the type through `type_id`, owns fields through `group_id`.
- `CustomField`: a field definition on a type (Issue #, Vintage). Reaches its account through the type.
- `Item`: a catalogued entry, such as "Amazing Spider-Man #1". Belongs to a collection, and optionally a type, category and set. Reaches its account through its collection.
- `Copy`: a single physical instance of an item, with its own condition and location. Three owned copies are three rows. The table is `copies`, and money is stored in cents.
- `ItemPhoto`: a photo of an item. `mainPhoto` is the one flagged `is_main`, served over a streamed route.
- `CustomFieldValue`: one item's value for one custom field.
- `Category`: groups items within a collection, and nests into itself through `parent_id`.
- `Set`: a named series of items to complete. Belongs to a collection through `collection_id`, and reaches its account through it. A `target_count` above zero is what makes the set count towards the completion statistics.
- `Tag`: a reusable label shared across the account, attached to items through a pivot.
- `Condition`: the state of a copy (New, Damaged). A null `account_id` marks a seeded system default shared across accounts.
- `CollectionView`: remembers one user's chosen items layout (grid, list, table) for one collection.
- `Location`: where a copy is physically stored. Nests into itself through `parent_id`.
- `Log`: the audit trail of user actions, and the source of the dashboard activity feed.
- `EmailSent`: a record of every email the app sent, with delivery tracking.
- `WebhookEndpoint`: a webhook destination. Scoped to a user rather than an account.
- `UserDeletionReason`: why someone deleted their user. Left unlinked on purpose so it outlives them.
- `Country`: a read only lookup of countries, seeded from `database/data`.

## Docker and self-hosting

Self-hosting with Docker is a supported use case, so keep the image building. One multi-stage `Dockerfile` runs three roles via `CONTAINER_ROLE` (web, queue, scheduler); the rest of the setup is in `docker-compose.yml`, `docker/`, `.env.docker.example` and `docker/README.md`.

In the same PR, reflect anything your change adds: a new PHP extension in the `Dockerfile`, a new or renamed env var in `.env.docker.example`, a new queue name in `docker-compose.yml`.

Migrations must be upgrade-safe: the entrypoint only runs `php artisan migrate --force`, so never use `migrate:fresh` or `migrate:refresh`, and avoid destructive migrations without a safe multi-step path. It also skips `route:cache` on purpose (a closure route lives in `routes/web.php`), so do not add routes that cannot be serialized.

## Guidelines for git and Github

- You MUST create a new branch when doing a new task, unless stated otherwise, based off of main branch. Make sure main is always up-to-date.
- Branche names MUST be of the format YYYYMMDD-{name}.
- You MUST follow conventional commits for commit messages.
- NEVER mention Claude Code in PR descriptions, PR comments, or issue comments.

## Writing something

Never use dashes (— or -) as punctuation in documentation or README files. Rephrase sentences using periods, commas, or parentheses instead.
