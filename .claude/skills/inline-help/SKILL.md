---
description: Add an inline help "?" popover next to a form field or a section/screen title, backed by a translated Markdown snippet. Use whenever the user asks for inline help, a help popover, or a "?" tooltip next to a field or a title. Triggers on "add inline help", "help popover", "help bubble", "tooltip", or a CLAUDE.md inline help requirement.
---

# Instructions

This mirrors what PR #176 (`feat: add inline help popovers backed by translated
documentation snippets`) established, and what the CLAUDE.md "Inline help and
documentation" section requires. It applies to two placements:

- **Field-level**: a `?` next to a form field label (`x-input`, `x-select`, or
  any field component that exposes a `helpId` prop).
- **Title-level**: a `?` next to a section or screen heading, most often an
  `<x-box title="...">` header, where there is no single field to anchor to.

Both placements share the same snippet format and the same `<x-help>`
component; only how the `id` gets wired into the view differs.

## 1. Choose the id

Follow the `{page}.{section}.{field}` convention from CLAUDE.md, dot separated,
snake_case per segment when a segment is more than one word (matches
`account_name`, not `accountName`):

- Field-level: `settings.general.currency` (page `settings`, section `general`,
  field `currency`).
- Title-level (no single field to anchor to): drop the field segment, e.g.
  `settings.getting_started` or `settings.delete_account` for the `x-box`
  sections on the account settings page.

The id is whatever the snippet's frontmatter `id:` says, not the filename, but
keep them identical for a project convention worth following. This is a
separate convention from the documentation portal's own `doc:` ids (see step
3), which are camelCase (`accounts.delete`, `gettingStarted.checklist`) -
don't mix the two styles up.

## 2. Wire the id into the view

**Field-level** (already supported by `x-input` and `x-select`):

```blade
<x-input id="name" name="name" :label="__('Account name')" helpId="settings.general.account_name" ... />
```

**Title-level**, next to an `<x-box title="...">` heading. `x-box`'s `title`
prop is a plain escaped string, so `<x-help>` cannot ride inside it. Add a
`helpId` prop to `resources/views/components/box.blade.php` (if it does not
already have one) mirroring the field components' pattern, and place
`<x-help>` right after the `<h2>`:

```blade
@props([
  'title' => null,
  'helpId' => null,
  ...
])

@isset($title)
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-2">
      <h2 class="mb-1 text-lg font-semibold text-ink">{{ $title }}</h2>
      @if ($helpId)
        <x-help :id="$helpId" />
      @endif
    </div>
    ...
  </div>
@endisset
```

Then at the call site:

```blade
<x-box title="{{ __('Delete account') }}" helpId="settings.delete_account">
```

For a heading that is not an `x-box` (a plain `<h1>`/`<h2>` written by hand,
like the page header in `account/index.blade.php`), skip the component change
and drop `<x-help :id="..." />` directly next to it, wrapped in the same
`flex items-center gap-2` pattern used everywhere else.

## 3. Write the English snippet

Create `docs/help/en/{id}.md`:

```markdown
---
id: settings.delete_account
kicker: Danger zone
title: Delete account
doc: accounts.delete
note_title: Cannot be undone
note: A one sentence callout for the single most important consequence.
---
One or two short paragraphs, blank line separated. Say what the thing is and
the one consequence or rule that matters. No filler.
```

Frontmatter fields, all parsed by `App\Services\HelpSnippets`:

- `id` (required): must match the `helpId`/`<x-help id>` used in the view.
- `title` (required): shown as the popover heading.
- `kicker` (optional): short uppercase label above the title (a category, not
  a restatement of the title).
- `doc` (optional): the frontmatter `id:` of an **existing** page under
  `docs/portal`, becomes the "Read more" footer link. Only set this when a
  matching portal page already exists (check with
  `grep -rn "^id: <candidate>" docs/portal/en`); do not invent an id that
  resolves to nothing; `DocumentationPortal::urlForId()` silently returns
  null and the footer just disappears, which is not a hard failure but is
  missed value.
- `note_title` / `note` (optional, both or neither): a short callout box for
  one consequence worth calling out on its own (irreversibility, no
  conversion, no undo). Skip it when the body already says everything needed.

Keep the body to one or two short paragraphs. Say what the thing is, then the
one rule or consequence that matters. No filler, no restating the title.

## 4. Translate into every locale

Translate the same file into every locale in `config('docs.locales')`
(currently `fr_FR`, `es_ES`, `de_DE`, `pt_BR`, `zh_CN`, `ja_JP`), at
`docs/help/{locale}/{id}.md`, same frontmatter shape. English only is not
enough: `HelpSnippets` falls back to the default locale automatically, but
that fallback exists for lag, not as a substitute for translating. Match the
tone and length of the English original; look at an existing translated
snippet (`docs/help/fr_FR/settings.general.currency.md` is a good reference)
for register.

The popover's chrome strings (`Help`, `Close`, `From the documentation`,
`Read more`) are already translated once in every `lang/*.json` file and
reused by every snippet; do not add them again.

## 5. Test

Extend the relevant Feature controller test (`assertSee` a distinctive phrase
from the new snippet's body, the way
`tests/Feature/Controllers/App/Account/AccountControllerTest.php`'s
`it('renders the field help popovers on the settings page', ...)` does) so a
regression that breaks resolution or the view wiring fails the suite. A new
generic `HelpSnippets` unit test is only needed if you changed the service
itself, not for adding another snippet, since the existing
`tests/Unit/Services/HelpSnippetsTest.php` cases already cover resolution,
fallback and the note/url branches generically.

## 6. Sanity check before committing

- `php artisan test --filter=<TouchedTest>` (or the full suite) passes.
- Every locale directory has the new file (`ls docs/help/*/<id>.md` should
  list one per configured locale).
- If a `doc:` id was used, confirm it resolves:
  `grep -rn "^id: <id>" docs/portal/en`.
