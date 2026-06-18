---
name: api-docs-writer
description: Write or update the public-facing marketing documentation page for API methods. Use when a new API controller is created, routes are added or changed, or when documentation pages are missing or out of sync with the codebase.
---

# API Docs Writer

This skill creates and maintains the public-facing API documentation pages on the marketing site. Each controller's endpoints are documented in a dedicated markdown files.

There is no need to create a controller and route, nor changing anything in the sidebar, since it's generated automatically.

## When to use this Skill

Use this Skill when:
- A new API controller or route group is added
- An existing route is renamed, moved, or removed
- A documentation page is missing for an implemented controller
- Documented parameters or response shapes change

---

## Step 1 — Research the API being documented

Before writing anything:
1. Read the relevant API controller (`app/Http/Controllers/Api/…`) to identify every public method.
2. Read `routes/api.php` to get the exact URL pattern, HTTP method, and route name for each endpoint.
3. Read the Eloquent Resource class (`app/Http/Resources/…`) to get the exact response shape.
4. Read the Actions involved to understand permission requirements (e.g. Owner/Admin-only vs any member).
5. Read sibling markdown files (e.g. `resources/views/marketing/docs/1.x/03-API/04-vaults/01-vault-management.md`) to follow established style and tone.

---

## Step 2 — Write the markdown documentation page

Create or update the markdown file at the appropriate path, for example:

```text
resources/views/marketing/docs/1.x/03-API/04-vaults/01-vault-management.md
```

API documentation pages are plain markdown documents enhanced with custom markdown directives. Do not use Blade layout wrappers or Blade documentation components.

### Endpoint order

Document endpoints in this order:

1. `GET` collection endpoint — list resources
2. `POST` collection endpoint — create resource
3. `GET` single-resource endpoint — get one resource
4. `PUT` or `PATCH` single-resource endpoint — update resource
5. `DELETE` single-resource endpoint — delete resource

Keep this order even if the controller methods are ordered differently.

### Page section order

Each documentation page should follow this structure:

1. Page title using a level-one markdown heading
2. `markdown-actions` block
3. `toc` block
4. Overview section

   * Left column: short explanation of the resource and global access rules
   * Right column: endpoint list inside a `code` block
5. One section per endpoint

   * Left column:

     1. Endpoint heading
     2. `description`
     3. URL parameters
     4. Query or body parameters
     5. Response attributes
   * Right column:

     1. Request/response example inside a `code` block

### Available markdown directives

#### `:::markdown-actions`

Adds documentation actions at the top of the page.

Use it with:

```markdown
:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions
```

#### `:::copy-for-llm`

Renders an action allowing the page content to be copied for use with an LLM.

Must be nested inside `markdown-actions`.

#### `:::view-as-markdown`

Renders an action allowing the raw markdown document to be viewed.

Must be nested inside `markdown-actions`.

#### `:::toc`

Defines the table of contents.

Use regular markdown links inside it:

```markdown
:::toc

- [List vaults](#list-vaults)
- [Create a vault](#create-a-vault)

:::/toc
```

The link anchors must match the endpoint headings.

#### `:::section`

Creates a page section.

Common options:

```markdown
:::section columns divided
...
:::/section
```

Use `columns` for a two-column layout. Use `divided` when the section should have visual separation from the next section.

The final endpoint section should usually omit `divided`.

#### `:::column`

Creates a column inside a `section columns` block.

Each two-column section should contain exactly two `column` blocks.

#### `:::description`

Wraps the explanatory text for an endpoint.

Use it immediately below the endpoint heading.

#### `:::parameters`

Creates a parameter or attribute group.

Use it for:

```markdown
:::parameters title="URL parameters"
:::/parameters

:::parameters title="Query parameters"
:::/parameters

:::parameters title="Body parameters"
:::/parameters

:::parameters title="Response attributes"
:::/parameters
```

When there are no parameters, use the `empty` attribute:

```markdown
:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters
```

#### `:::attribute`

Documents one parameter or response field.

Use it inside a `parameters` block:

```markdown
:::attribute name="data.attributes.name" type="string"
The name of the vault.
:::/attribute
```

Add `required` when the field is required:

```markdown
:::attribute name="name" type="string" required
The name of the vault.
:::/attribute
```

#### `:::code`

Renders a code example panel.

Use it for endpoint lists and response examples:

````markdown
:::code title="/api/vaults/{id}" verb="GET"

```json
{
  "data": {}
}
````

:::/code

````

For endpoint overview blocks, omit `verb` and use `title="Endpoints"`.

For delete endpoints returning `204 No Content`, use:

```markdown
:::code title="/api/vaults/{id}" verb="DELETE"

```text
No response body
````

:::/code

```
```

---

## Step 4 — Write the test

Create a PHPUnit feature test in `tests/Feature/Controllers/Marketing/Docs/`. One test per docs page; it asserts the route returns HTTP 200.

**Important:** `php artisan make:test` incorrectly nests the file under `tests/Feature/Feature/…`. Always move the generated file to the correct location:
```bash
mv tests/Feature/Feature/Controllers/Marketing/Docs/ApiOfficeTypeControllerTest.php \
   tests/Feature/Controllers/Marketing/Docs/ApiOfficeTypeControllerTest.php
rm -rf tests/Feature/Feature
```
