---
name: controllers
description: Use when working with controllers.
---

# Controllers

## Rules

- Check the other controllers in the project for reference, and try to follow the same structure and conventions.
- Controllers should be as thin as possible, and actions should contain the business logic. Models should only contain relationships and accessors/mutators.
- Controllers should be testable.

## Things to do

- Only these methods: `index`, `show`, `store`, `update`, `destroy`
- No domain logic — call Actions
- Validate inline (no FormRequests)
- Use `$request->attributes->get('vault')` to get the vault (set by `vault.api` middleware)
- Use `$request->user()` — never `Auth::user()`
- Scope all resource lookups to the vault: `$vault->offices()->findOrFail($officeId)`
- Validate the request data, but do not sanitize it. In the validation, do not check if an object exists by checking if the id exists in the database - this is done in Actions. Make sure the validation rules match the fields of the model, as defined in the migration (ie length of a given string).

### If it's a web controller

- [ ] Return views, not JSON.
- [ ] Always pass validated data to the action.
- [ ] Do not compact data to a view. Instead, pass an array with keys that represent what the data is, like `['journals' => $journals]` instead of `compact('journals')`.
