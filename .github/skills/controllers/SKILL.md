---
name: controllers
description: Use when working with controllers.
---

# Controllers

## Rules

- Check the other controllers in the project for reference, and try to follow the same structure and conventions.
- Controllers should be as thin as possible, and actions should contain the business logic. Models should only contain relationships and accessors/mutators.
- Controllers are 100% testable.

## Checklist

- [ ] If possible, only take the Request as a parameter, and extract everything else from it (like the vault, or the user), like `$vault = $request->attributes->get('vault');`.
- [ ] Validate the request data, but do not sanitize it. In the validation, do not check if an object exists by checking if the id exists in the database - this is done in Actions.
- [ ] Always pass validated data to the action.
- [ ] Do not compact data to a view. Instead, pass an array with keys that represent what the data is, like `['journals' => $journals]` instead of `compact('journals')`.
