---
name: api-writer
description: Build a complete API surface for a given model or concept. Use when the user asks to add API methods, expose a resource via API, or mirror a web controller as an API. Activates when user mentions API methods, API controller, API routes, or wants to expose an existing web resource via API.
---

# API writer

## Step 1 — Study the existing web controller

Read the existing web controller, if it exists (`app/Http/Controllers/App/…`). Extract:

- The **resource name** (singular, e.g. `Office`)
- Which **actions** are used (`CreateXxx`, `UpdateXxx`, `DestroyXxx`)
- The **validated fields** and their rules
- The **permission model** (who can do what — check the action's `validate()` method)

---

## Step 2 — Create the Eloquent Resource

Create `app/Http/Resources/{Resource}Resource.php`.

Rules:
- `type` field is snake_case resource name (e.g. `'office'`)
- `id` is cast to string
- `attributes` contains all relevant model fields; timestamps as Unix integers via `->timestamp`
- `links.self` points to the `show` route for this resource

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Office
 */
class OfficeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'office',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                // ... other fields ...
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => route('api.organization.adminland.office.show', [
                    'id' => $this->organization_id,
                    'officeId' => $this->id,
                ]),
            ],
        ];
    }
}
```

---

## Step 3 — Create the API controller

Load instructions from [the controller skill](../controllers/SKILL.md) and apply them, with the following adjustments.

Create `app/Http/Controllers/Api/[XXX]/{Resource}Controller.php`. 

It MUST match the web controller's namespace pattern (e.g. `Adminland`), but under `Api` instead of `App`.

Rules (from `controllers` skill):
- Only these methods: `index`, `create`, `show`, `update`, `destroy`
- No domain logic — call Actions
- Validate inline (no FormRequests)
- Use `$request->attributes->get('vault')` to get the vault (set by `vault.api` middleware)
- Use `$request->user()` — never `Auth::user()`
- Return `AnonymousResourceCollection` from `index`, `JsonResponse` from `show`/`store`/`update`, `Response` (204) from `destroy`
- Scope all resource lookups to the vault: `$vault->offices()->findOrFail($officeId)`

---

## Step 4 — Register routes

- Add routes to `routes/api.php` inside the `vault.api` middleware group. Follow the existing pattern exactly.
- Add the `use` import in alphabetical order with other `Adminland` controller imports.
- Use something like `->where('gender', '[1-9][0-9]*')` to enforce numeric IDs in the URL when applicable.

---

## Step 5 — Write tests

Create `tests/Feature/Controllers/Api/{Resource}ControllerTest.php`.

Rules:
- Use `RefreshDatabase`
- Use `#[Test]` attributes
- Use `Sanctum::actingAs($user)` for auth
- Use `$this->json('METHOD', '/api/…', $data)` — never `$this->actingAs()->…`
- Never call `for()` on factories. Pass explicit `['key' => $value]` arrays
- Never peek in the database — only assert HTTP status and JSON structure
- Define a `$jsonStructure` property once and reuse it

**Required test cases:**
1. `it_lists_{resources}_of_a_vault` — 200 + count
2. `it_restricts_listing_{resources}_to_vault_members` — 403 for non-member
3. `it_can_show_a_{resource}` — 200 + structure
4. `it_returns_404_when_showing_a_{resource}_from_another_vault` — 404
5. `it_can_create_a_{resource}` — 201 + structure
6. `it_returns_404_when_a_user_doesnt_have_permission_to_create_a_{resource}` — 404
7. `it_can_update_a_{resource}` — 200 + structure
8. `it_returns_404_when_a_user_doesnt_have_permission_to_update_a_{resource}` — 404
9. `it_can_destroy_a_{resource}` — 204 no content
10. `it_returns_404_when_a_user_doesnt_have_permission_to_destroy_a_{resource}` — 404

---

## Step 6 — Marketing docs

Load and follow the **`api-docs-writer`** skill.

Summary:
- Create `app/Http/Controllers/Marketing/Docs/Api{Resource}Controller.php`
- Add markdown document in the appropriate location.
- Create the test file, which should match how we wrote `tests/Feature/Controllers/Marketing/Docs/ApiVaultManagementControllerTest.php`.
    - Only assert that the page loads, without checking for specific content, to avoid brittle tests.

---

## Step 7 — Bruno documentation

- Create a folder in the Bruno doc folder in `docs/Bruno`, following the existing pattern.
- Create 5 `.bru` files for the 5 API methods, following the existing pattern.
