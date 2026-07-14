---
name: migrations
description: Use when creating or editing database migrations in the project.
---

# Migrations

## Rules

- When adding foreign keys, do not use the `constrained()` method.

❌ Don't
```php
$table->foreignId('account_id')->comment('account the user belongs to')->constrained()->cascadeOnDelete();
```

Do
```php
$table->unsignedBigInteger('user_id')->nullable()->comment('user who performed the action');
...
// (at the bottom of the migration)
$table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
```
