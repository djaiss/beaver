---
name: permissions-and-roles
description: Permissions are what a user is allowed to do within an application. Use when working with permissions.
---

# Permissions and roles

## Rules

- Permissions are managed in app/Enums/PermissionEnum.php.
- Check the other permissions in the project for reference.
- A permission is linked to a role.
- You can not add a new role yourself.
- All the available default roles are defined below. Some roles are not yet supported but they will be. Don't do nothing with them for now, but keep them in mind for the future.

## Checklist

- [ ] Always sanitize data first
- [ ] Always validate data: permissions, existence of related models, link to organization,...
- [ ] Create Permission if needed
- [ ] Add the permission information in the app/Jobs/PopulateOrganization.php file, under `addDefaultPermissions()` method
- [ ] Add the permission to the right role in the app/Jobs/PopulateOrganization.php, under `mapPermissionsWithRoles()` method
- [ ] Add tests for the new permission in tests/Unit/Jobs/PopulateOrganizationTest.php

## Roles

### Owner

Full control over the organization.

Can manage:
- organization settings
- staff members
- roles and permissions
- domains
- billing and ownership

Highest level of responsibility.

---

### Administrator

Operational administrator of the library.

Can manage:
- catalog
- patrons
- circulation
- branches and locations
- most settings

High level of responsibility, but does not own the organization.

---

### Librarian

General-purpose library staff role.

Can:
- manage catalog items
- manage patrons
- process loans and returns
- manage holds and reservations

Primary operational role in the library.

---

### Cataloger

Specialized metadata role.

Responsible for:
- works and editions
- authors and publishers
- imports
- metadata quality

Limited responsibility outside the catalog.

---

### Circulation Staff

Front-desk circulation role.

Can:
- check items in and out
- renew loans
- place and fulfill holds
- assist patrons

Focused on day-to-day circulation operations.

---

### Volunteer

Limited-access helper role.

Can assist with:
- inventory
- shelving
- scanning returns
- simple operational tasks

Very limited responsibility and permissions.

---

### Patron

Public library member with an account.

Can:
- access their account
- view loans
- renew loans
- manage holds

No staff or administrative permissions.
