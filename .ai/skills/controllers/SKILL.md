---
name: controllers
description: Conventions for writing thin HTTP controllers that validate input and delegate to Actions. Use when creating or editing controllers in app/Http/Controllers (web, API, or marketing).
---

# Controllers

## Rules

- Check the other controllers in the project for reference, and try to follow the same structure and conventions.
- Controllers should be as thin as possible, and actions should contain the business logic. Models should only contain relationships and accessors/mutators.
- Controllers should be testable.

## Things to do

- Only these methods: `index`, `new`, `create`, `show`, `edit`, `update`, `destroy`
- No domain logic — call Actions
- Validate inline (no FormRequests)
- Use `$request->attributes->get('vault')` to get the vault (set by middlewares)
- Use `$request->user()` — never `Auth::user()`
- Scope all resource lookups to the vault: `$vault->offices()->findOrFail($officeId)`
- Read route parameters with `$request->route()->parameter('gender')`
- Validate the request data, but do not sanitize it. In the validation, do not check if an object exists by checking if the id exists in the database - this is done in Actions. Make sure the validation rules match the fields of the model, as defined in the migration (ie length of a given string).
- Instantiate the Action and call `->execute()`, passing `user: $request->user()` and the validated data with named arguments.
- Type-hint the return of every method (`View`, `RedirectResponse`, `JsonResponse`, `AnonymousResourceCollection`, `Response`).

### If it's a web controller

- Return views, not JSON.
- Always pass validated data to the action.
- Do not compact data to a view. Instead, pass an array with keys that represent what the data is, like `['journals' => $journals]` instead of `compact('journals')`.
- Wrap scoped `findOrFail()` lookups in a try/catch on `ModelNotFoundException` and `abort(404)`.
- Redirect with `to_route(...)->with('status', __('Changes saved'))` after a mutation.

### If it's an API controller

- Return an API Resource (e.g. `GenderResource`), never raw arrays/JSON.
- Set the status code explicitly: `200` for show/update, `201` for create via `->response()->setStatusCode(...)`, and `response()->noContent(204)` for destroy.
- For `index`, return `Resource::collection($paginated)` and paginate, clamping `per_page` to `config('app.maximum_items_per_page')`.
- Let scoped `findOrFail()` throw — no try/catch (the framework returns 404).
