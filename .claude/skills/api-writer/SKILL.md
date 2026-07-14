---
name: api-writer
description: Build a complete API surface for a given model or concept. Use when the user asks to add API methods, expose a resource via API, or mirror a web controller as an API. Activates when user mentions API methods, API controller, API routes, or wants to expose an existing web resource via API.
---

# API writer

The API mirrors the web controllers under `Api`, returns Eloquent Resources, and reuses the same Actions. Use the existing `genders` API (controller, resource, routes, tests) as the reference implementation.

## Step 1 — Study the existing web controller

Read the web controller, if it exists (`app/Http/Controllers/App/…`). Extract:

- The **resource name** (singular, e.g. `Gender`)
- Which **Actions** are used (`CreateXxx`, `UpdateXxx`, `DestroyXxx`)
- The **validated fields** and their rules
- The **permission model** — check the Action's `validate()` method (who can do what)

## Step 2 — Create the Eloquent Resource

Create `app/Http/Resources/{Resource}Resource.php`.

- Add a `/** @mixin {Model} */` docblock and extend `JsonResource`.
- `type` is the snake_case resource name (e.g. `'gender'`).
- `id` is cast to string.
- `attributes` contains the relevant model fields; render timestamps as Unix integers via `->timestamp` (use `?->timestamp` for nullable ones like `updated_at`).
- `links.self` points to the `show` route, scoped by vault.

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Gender
 */
class GenderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'gender',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.gender.show', [
                    'id' => $this->vault_id,
                    'gender' => $this->id,
                ]),
            ],
        ];
    }
}
```

## Step 3 — Create the API controller

Follow the [controllers skill](../controllers/SKILL.md), then create `app/Http/Controllers/Api/[XXX]/{Resource}Controller.php`. It MUST match the web controller's namespace (e.g. `Vault\Adminland`) but under `Api` instead of `App`.

- Only these methods: `index`, `show`, `create`, `update`, `destroy`.
- No domain logic — call the Actions and pass `user: $request->user()` with named arguments.
- Get the vault with `$request->attributes->get('vault')` (set by the `vault.api` middleware).
- Scope every lookup to the vault and let it throw: `$vault->genders()->findOrFail($id)` (no try/catch — the framework returns 404).
- Return types: `AnonymousResourceCollection` from `index`, `JsonResponse` from `show`/`create`/`update`, `Response` from `destroy`.
- Status codes: `200` for `show`/`update`, `201` for `create` via `->response()->setStatusCode(...)`, and `response()->noContent(204)` for `destroy`.
- `index` returns `{Resource}::collection($paginated)`, ordered, and paginated with `per_page` clamped to `config('app.maximum_items_per_page')`.

## Step 4 — Register routes

- Add routes to `routes/api.php` inside the `vault.api` middleware group (prefix `vaults/{id}`).
- Add the controller `use` import in alphabetical order with the other imports.
- Name routes `vault.{resource}` for the index and `vault.{resource}.{action}` for the rest (the group adds the `api.` prefix).
- Constrain numeric IDs with `->where('gender', '[1-9][0-9]*')`.

## Step 5 — Write tests

Create `tests/Feature/Controllers/Api/[XXX]/{Resource}ControllerTest.php`.

- Use `RefreshDatabase` and `#[Test]` attributes.
- Authenticate with `Sanctum::actingAs($user)`.
- Make requests with `$this->json('METHOD', '/api/vaults/'.$vault->id.'/…', $data)`.
- Set up data with explicit `['key' => $value]` arrays on factories; never use `for()`.
- Assert HTTP status and JSON only — define a `$jsonStructure` property once and reuse it with `assertJsonStructure()`/`assertJsonPath()`/`assertJsonCount()`.

Required cases (a non-member never reaches the controller; cross-vault and missing-permission both surface as 404):
1. `it_lists_the_{resources}_of_a_vault…` — 200 + count + ordering
2. `it_does_not_list_{resources}_from_another_vault` — 200 + empty list
3. `it_can_show_a_{resource}` — 200 + structure
4. `it_returns_not_found_for_a_{resource}_from_another_vault` — 404
5. `it_can_create_a_{resource}` — 201 + structure
6. `it_validates_the_…_when_creating_a_{resource}` — 422 + `assertJsonValidationErrors`
7. `it_restricts_{resource}_creation_to_the_vault_owner` — 404
8. `it_can_update_a_{resource}` — 200 + structure
9. `it_restricts_{resource}_updates_to_the_vault_owner` — 404
10. `it_can_delete_a_{resource}` — 204 + `assertModelMissing`
11. `it_restricts_{resource}_deletion_to_the_vault_owner` — 404

## Step 6 — Marketing docs

Load and follow the **`api-docs-writer`** skill.

- Create `app/Http/Controllers/Marketing/Docs/Api{Resource}Controller.php`.
- Add the markdown document in the appropriate location.
- Create the test, matching `tests/Feature/Controllers/Marketing/Docs/ApiVaultManagementControllerTest.php` — only assert the page loads, to avoid brittle tests.
