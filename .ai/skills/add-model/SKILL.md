---
name: add-model
description: "Adds a new model to the Laravel application. Use when the user wants to create a new Eloquent model, including its migration, factory, and seeder. Triggers on model creation, migration generation, and related tasks."
---

# Adding a Model in Laravel

Use the following checklist

```
- [ ] If no migrations already exist, create a migration for the given fields.
- [ ] If a migration exists, read the migration that was generated for the new model to understand the database schema changes.
- [ ] Make sure each field of the migration is commented, except id, timestamps.
- [ ] Create the model object in the app/Models directory. Ensure it extends the Eloquent Model class and includes any necessary relationships, fillable properties, or casts. Follow existing patterns in the codebase for consistency.
- [ ] Add the corresponding model factory. Use fake data to populate the factory for testing purposes.
- [ ] Create the test file for the model. It is located in /tests/Unit/Models/ and should be named after the model (e.g., UserTest.php for a User model).
- [ ] Add test methods to test the existence of relationships.
- [ ] Check each related model in the migration to add the proper tests in those models. For instance, if the new model has a belongsTo relationship with another model, add a test in that model to check the hasMany relationship.
```
