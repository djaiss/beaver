---
id: accounts.usersAndRoles
title: Accounts, users, and roles
slug: accounts-users-and-roles
section: core-concepts
---

# Accounts, users, and roles

KolleK is built around one workspace, the account, and the people who share it. This page explains the boundary and the permission model in plain language, so nothing about access ever surprises you.

## The account is the boundary

An **account** is a private workspace. Every collection, item, copy, type, tag, and location lives inside exactly one account. Nothing leaks between accounts, and nobody outside yours can see in unless you deliberately [share a collection](12-visibility-and-sharing.md).

When Emma registered, KolleK created two things at once: her personal user, and a fresh account that she owns. If she invites her partner Sam, he joins her account and works in the same catalogue.

## One person, one account

A **user** is one authenticated person, tied to one email address, and a user belongs to exactly one account.

:::note
The same email cannot be in two accounts. Someone who already has their own account cannot accept an invitation to yours. If they want to join you, they would need to use a different email address, or delete their own account first.
:::

## The three roles

Every member of an account has one role, chosen when they are invited and changeable later by an owner:

- A **viewer** can browse everything in the account, but cannot create or change anything. Emma's friend Leo is a viewer: he can admire the catalogue, not edit it.
- An **editor** can create and change catalogue content: collections, items, copies, photos, and all the history records. Sam is an editor.
- An **owner** can do everything an editor can, and also manage the account itself: invite and remove members, change roles, manage account settings, and delete the account. Emma is the owner.

Reading is open to every member, including viewers. Writing needs editor or owner. Administering the account needs owner. The [roles in practice](../8-collaboration/4-roles-in-practice.md) page maps this to concrete tasks if you want the full table.

An account must always keep at least one owner. KolleK will not let the last owner be demoted or removed, so an account can never lock itself out.

## One flag that is not a role

If you ever hear about an **instance administrator**, that is something else entirely. It is a server wide flag for whoever operates the KolleK installation itself. It grants nothing extra inside that person's own account, and it has nothing to do with viewer, editor, or owner. It is covered in [the instance administration panel](../14-self-hosting/10-instance-administration-panel.md) for operators.

## Where to next

- Bring someone in with [Invite people to your account](../8-collaboration/2-invite-people.md).
- Change what a member can do in [Manage members and roles](../8-collaboration/3-manage-members-and-roles.md).
- Continue the concepts with [Collections](4-collections.md).
