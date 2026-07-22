# Code Style

## Core Philosophy

- Laravel provides the most value when you write things the way Laravel intended you to write.
- Follow documented Laravel approaches; justify any deviations.

## General PHP

- Follow PSR-1, PSR-2, and PSR-12 standards.
- Use camelCase for non-public-facing string-like elements.
- Avoid using the `final` keyword; assume users write tests for overridden behavior.
- Prefer short nullable notation (`?string`) over union types with null.
- Use the `void` return type when a method returns nothing.

## Typed Properties & Type Hints

- Always type properties when possible.
- Use actual type declarations instead of docblock `@var` annotations.

## Enums

- Enum case names use PascalCase.

## Docblocks

- Omit docblocks for fully type-hinted methods unless a description adds context.
- Descriptions must use complete sentences with periods.
- Import class names in docblocks rather than using fully qualified names.
- Use single-line format when possible.
- For multiple types, list the most common type first.
- If one parameter requires documentation, add docblocks for all parameters and the return value.
- For iterables, specify key and value types using angle brackets.
- For arrays with fixed keys, use `{}` notation.

## Constructor Property Promotion

- Use promotion when all properties can be promoted.
- Place each promoted parameter on a separate line with a trailing comma.

## Traits

- Each trait gets its own `use` statement for cleaner diffs.

## Ternary Operators

- Short expressions can fit on one line.
- Longer expressions require each portion on a separate line.

## If Statements

- Always use curly brackets; never omit them.
- Place the unhappy path first with early returns, leaving the happy path last and unindented.
- Avoid `else`; refactor using early returns or ternary operators.
- Prefer separate if statements over compound conditions for easier debugging.

## Comments

- Minimize comments by writing expressive code.
- Format single-line comments with a space before the text.
- Multi-line comments use `/*` with a single `*` on the first line.
- Refactor comments into named functions when possible.

## Test Classes

- Keep test-specific classes within the same test file when possible.
- Extract to dedicated classes only when reused across tests.

## Whitespace

- Add blank lines between statements to allow breathing room.
- Single-line equivalent operations may be grouped together.
- Don't add empty lines between `{}` brackets.

## Configuration

- Configuration file names use kebab-case.
- Configuration keys use snake_case.
- Avoid the `env()` helper outside config files; create config values from env variables.
- Add service credentials to `config/services.php` rather than creating separate files.

## Artisan Commands

- Command names use kebab-case.
- Always output a confirmation message on successful completion.
- When processing items, output before processing each item, and provide a summary count at the end.

## Routing

- Public-facing URLs use kebab-case.
- Prefer tuple notation using class arrays over string notation.
- Route names use camelCase.
- Place the HTTP verb first, followed by other options.
- Route parameters use camelCase.
- Don't start routes with `/` except for the root path `/`.
- Carry navigational state in the path, not the query string. Which tab is open, which record is selected, which sub view is shown: each of these is its own path segment and its own URL (for example `support/open` and `support/open/42`, never `support?tab=open&ticket=42`). A query string is reserved for the few genuinely free-form inputs where it is the conventional tool: a search box (`?search=`) and pagination (`?page=`). If you reach for a query parameter to remember a choice the user made by clicking, model it as a route instead.

## API Routing

- Resource names use the plural form in kebab-case.
- Limit deep nesting; use nesting only when it provides necessary context.

## Controllers

- Resource controllers use plural resource names.
- Stick to default keywords (`index`, `new`, `create`, `show`, `edit`, `update`, `destroy`).
- Extract separate controllers for additional actions.

## Views

- View files use camelCase.

## Validation

- Use array notation for multiple rules instead of pipe-separated strings.
- Custom validation rules use snake_case.

## Blade Templates

- Use four spaces for indentation.
- Don't add spaces after control structure keywords.

## Authorization

- Policies use camelCase.
- Use default CRUD words; replace `show` with `view`.

## Translations

- Use the `__()` function instead of `@lang` in Blade.

## Naming Classes

- Controllers: plural resource name + `Controller` suffix; non-resourceful/invokable controllers use the action name + `Controller`.
- Resources/Transformers: plural form + `Resource` or `Transformer` suffix.
- Jobs: describe the action performed.
- Events: use tense to indicate timing (before vs. after).
- Listeners: action name + `Listener` suffix.
- Commands: add a `Command` suffix to avoid collisions.
- Mailables: add a `Mail` suffix.
- Enums: no special prefix; the name should clearly indicate it's an enum.
