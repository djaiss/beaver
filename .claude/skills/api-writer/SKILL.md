---
name: api-writer
description: Build a complete API surface for a given model or concept. Use when the user asks to add API methods, expose a resource via API, or mirror a web controller as an API. Activates when user mentions API methods, API controller, API routes, or wants to expose an existing web resource via API.
---

# API writer

The API mirrors the web controllers under `Api`, returns Eloquent Resources, and reuses the same Actions. Use the existing `collections` API (controller, resource, routes, tests, docs) as the reference implementation.

A user belongs to exactly one account, so there is no tenant prefix in the URLs: every endpoint is scoped through `$request->user()->account`.

## Step 1 — Study the existing web controller

Read the web controller, if it exists (`app/Http/Controllers/App/…`). Extract:

- The **resource name** (singular, e.g. `Collection`)
- Which **Actions** are used (`CreateXxx`, `UpdateXxx`, `DestroyXxx`)
- The **validated fields** and their rules
- The **permission model** — check the Action's `validate()` method (owners and editors write, viewers read)

## Step 2 — Create the Eloquent Resource

Create `app/Http/Resources/{Resource}Resource.php`. Use `CollectionResource` as the reference.

- Add a `/** @mixin {Model} */` docblock and extend `JsonResource`.
- `type` is the snake_case resource name (e.g. `'collection_type'`).
- `id` is cast to string.
- `attributes` contains the relevant model fields; render timestamps as Unix integers via `->timestamp` (use `?->timestamp` for nullable ones like `updated_at`).
- `links.self` points to the `show` route (e.g. `route('api.collections.show', $this->id)`; nested resources pass both parameters).

## Step 3 — Create the API controller

Follow the [controllers skill](../controllers/SKILL.md), then create `app/Http/Controllers/Api/{Resource}Controller.php`, mirroring the web controller's location under `Api` instead of `App` (auth lives in `Api/Auth`, user-scoped administration in `Api/Administration`).

- Only these methods: `index`, `show`, `create`, `update`, `destroy`. Extract extra actions to their own controller (e.g. `CollectionTypeCollectionController` for the sync endpoint).
- No domain logic — call the Actions and pass `user: $request->user()` with named arguments.
- Read route parameters with `$request->route()->parameter('collection')`.
- Scope every lookup to the account and let it throw: `$request->user()->account->collections()->findOrFail($id)` (no try/catch — the framework returns 404).
- Return types: `AnonymousResourceCollection` from `index`, `JsonResponse` from `show`/`create`/`update`, `Response` from `destroy`.
- Status codes: `200` for `show`/`update`, `201` for `create` via `->response()->setStatusCode(...)`, and `response()->noContent(204)` for `destroy`.
- `index` returns `{Resource}::collection($paginated)`, ordered, and paginated with `per_page` clamped to `config('app.maximum_items_per_page')`.

## Step 4 — Register routes

- Add routes to `routes/api.php` inside the `auth:sanctum` middleware group.
- Add the controller `use` import in alphabetical order with the other imports.
- URLs are plural kebab-case (`collection-types`), route parameters camelCase (`{collectionType}`).
- Name routes `{resource}` for the index and `{resource}.{action}` for the rest, camelCase (the group adds the `api.` prefix): `collections`, `collections.show`, `collectionTypes.customFields.update`.
- Constrain numeric IDs with `->where('collection', '[1-9][0-9]*')`.

## Step 5 — Write tests

Create `tests/Feature/Controllers/Api/{Resource}ControllerTest.php`. Match the Pest style of the existing files there (e.g. `CollectionControllerTest.php`): `it()` closures, `uses(RefreshDatabase::class)`, and a `$this->jsonStructure` set in `beforeEach`.

- Authenticate with `Sanctum::actingAs($user)`; `User::factory()` creates an account owner.
- Make requests with `$this->json('METHOD', '/api/…', $data)`.
- Fake the queue with `Queue::fake()` in tests that hit an Action (Actions dispatch `LogUserAction`).
- Set up data with explicit `['key' => $value]` arrays on factories; never use `for()`.
- Assert HTTP status and JSON only — reuse `$this->jsonStructure` with `assertJsonStructure()`/`assertJsonPath()`/`assertJsonCount()`.
- Use `assignUserToAccount(user, account, role)` from the `TestCase` to build a viewer for the permission cases.

Required cases (cross-account and missing-permission both surface as 404):
1. `it lists the {resources} of the account…` — 200 + count + ordering
2. `it does not list {resources} from another account` — 200 + empty list
3. `it shows a {resource}` — 200 + structure
4. `it returns not found for a {resource} from another account` — 404
5. `it creates a {resource}` — 201 + structure
6. `it validates the … when creating a {resource}` — 422 + `assertJsonValidationErrors`
7. `it restricts {resource} creation to owners and editors` — viewer gets 404
8. `it updates a {resource}` — 200 + structure
9. `it restricts {resource} updates to owners and editors` — 404
10. `it deletes a {resource}` — 204 + `assertModelMissing` (or `assertSoftDeleted` for soft-deleting models)
11. `it restricts {resource} deletion to owners and editors` — 404

## Step 6 — Document the endpoints

Load and follow the **`api-docs-writer`** skill: add the endpoints to a definition file in `resources/docs/api`, which feeds the docs portal at `/docs`.

This step is not optional — `Tests\Unit\Services\ApiDocumentationTest` asserts that every route in `routes/api.php` is documented, so the suite fails until the new endpoints have documentation.
