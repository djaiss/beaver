---
name: api-docs-writer
description: Write or update the public-facing marketing documentation page for API methods. Use when a new API controller is created, routes are added or changed, or when documentation pages are missing or out of sync with the codebase.
---

# API docs writer

Each API controller is documented in one markdown file under `resources/views/marketing/docs/1.x/03-API/…`. The page, route, and sidebar are generated automatically — no controller or route to create. Use `04-vaults/02-gender-management.md` as the reference.

## Step 1 — Research

Read, for the resource being documented:
- the API controller (`app/Http/Controllers/Api/…`) — every public method,
- `routes/api.php` — exact URL, verb, and route name per endpoint,
- the Eloquent Resource (`app/Http/Resources/…`) — exact response shape,
- the Actions' `validate()` — permissions (owner-only vs any member),
- a sibling `.md` page — to match tone and structure.

## Step 2 — Write the markdown page

Create the file next to its siblings; the numeric prefix sets the order (e.g. `03-API/04-vaults/02-gender-management.md`). The file path maps to its URL with prefixes stripped (`/docs/1.x/api/vaults/gender-management`).

Pages are plain markdown with custom `:::directive … :::/directive` blocks — no Blade. Page structure:

1. `# Title`
2. `:::markdown-actions` wrapping `:::copy-for-llm` and `:::view-as-markdown` (all with `url="{{docs.markdown_url}}"`)
3. `:::toc` — markdown links whose anchors match the endpoint headings
4. Overview `:::section columns divided` — left `:::column` describes the resource and access rules; right `:::column` lists endpoints in a `:::code title="Endpoints"` `text` block
5. One `:::section columns divided` per endpoint — left column has the `## heading`, `:::description`, then `:::parameters` groups; right column has a `:::code` response example. Omit `divided` on the last section.

Document endpoints in this order regardless of controller order: list (`GET`), get one (`GET`), create (`POST`), update (`PUT`/`PATCH`), delete (`DELETE`).

### Directives

- `:::section columns divided` / `:::column` — two-column layout; `divided` adds a separator (drop it on the final section).
- `:::description` — explanatory text under an endpoint heading.
- `:::parameters title="…"` — a group: `URL parameters`, `Query parameters`, `Body parameters`, or `Response attributes`. Add `empty="…"` when there are none.
- `:::attribute name="…" type="…"` — one field (add `required` when required); description goes inside.
- `:::code title="…" verb="GET"` — a request/response panel holding a fenced `json` (or `text`) block. Use `title="Endpoints"` without `verb` for the overview list, and a `text` block reading `No response body` for `204` deletes.

Match types and example values to the Resource (timestamps are integers, `updated_at` is `integer|null`, `id` is a string).

## Step 3 — Write the test

Add `tests/Feature/Controllers/Marketing/Docs/Api{Name}ControllerTest.php` with two `#[Test]` methods that `assertOk()`: one for the page URL and one for the same URL with a `.md` suffix. Assert status only, never content.

`php artisan make:test` nests the file under `tests/Feature/Feature/…`; move it to the correct path and `rm -rf tests/Feature/Feature` afterwards.
