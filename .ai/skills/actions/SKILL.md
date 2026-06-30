---
name: actions
description: Actions are what the user does within an application. Use when working with actions.
---

# Actions

## Rules

- Check the other actions in the project for reference, and try to follow the same structure and conventions.
- Actions are the only place where you can write business logic. Controllers should be as thin as possible, and models should only contain relationships and accessors/mutators.
- Actions are 100% testable.
- If an action does something for a user, we should always log what the user did.
- Always use Eloquent in an action, if possible.
- Actions must do as fewer DB queries as possible.

## Structure

- One action per class, with a single public `execute()` method that returns the affected model.
- Pass inputs through a constructor using promoted `readonly` properties (`User`, then the `Vault`/model, then the data).
- `execute()` only orchestrates small private steps in order: `sanitize()`, `validate()`, the work (`create()`/`update()`/`destroy()`), then `log()`.
- Sanitize strings with `TextSanitizer`; throw `ModelNotFoundException` from `validate()` when the user is not in the vault or lacks the role.
- Dispatch `LogUserAction` on the `low` queue.

## Action Naming Conventions

Actions should represent what a user wants to do, or what the system needs to do.
The verb should try to follow when possible, the appropriate RESTful method names, like `CreateXX`, `UpdateXX` or `DestroyXX`.

## Checklist

- Always sanitize data first
- Always validate data: permissions, existence of related models, link to vault,...
- Do what the action is supposed to do
- Add the case to `UserActionEnum` if it's a user action
- Add the mapping in `translationKey()` method if it's a user action
- Add the translation to `lang/en.json` (and other locales) if it's a user action
- Log the action for the user, include vault if applicable
- Write test for the action, and test all edge cases
