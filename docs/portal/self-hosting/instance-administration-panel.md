# The instance administration panel

The instance administration panel at `/instance-admin` is where an [instance administrator](grant-instance-administrator-access.md) looks across every account on the server: how many there are, who is in them, and the handful of destructive actions only an operator should hold. This page describes what the panel can do, and just as important, what it deliberately cannot.

If you run a personal instance with one account, you may never need this panel. It earns its keep on shared instances, such as a club or family server with several accounts.

:::note
The panel only appears for users carrying the instance administrator flag. Anyone else who visits `/instance-admin` gets a not found page, not an access denied one, so the panel never announces its existence.
:::

## The overview

The panel opens on an overview of the whole instance:

- Counts of **accounts**, **users**, **collections**, and **items** across the server.
- **Accounts created this month** and **active users this month**, so you can see whether the instance is growing or quiet.
- A chart of **signups per month** over the last twelve months.

These numbers are instance wide. They do not reveal the contents of anyone's catalogue.

## Browsing accounts

The **Accounts** area lists every account on the instance, 25 per page, with each account's member count and collection count.

You can search accounts **by a member's email address** and filter by role. Searching by account or person name is not possible, because names are encrypted in the database and cannot be matched there. Email is the reliable handle.

Opening an account shows its members, sorted owners first, then editors, then viewers, along with the account's collection and item counts and its fifteen most recent activity log entries.

## The destructive actions

Three actions in the panel change or remove data, and none of them can be undone:

- **Delete an account**, which removes the account with every collection, item, copy, member, and all history in it.
- **Delete a user**, which removes that person from their account.
- **Toggle another user's administrator flag**, which grants or revokes instance administration for someone else.

:::warning
Deleting an account or a user from this panel is immediate and permanent. Nothing passes through the trash, and there is no restore. Check twice that you have the right account or person before confirming.
:::

Two safeguards protect the instance itself: an administrator cannot revoke their own flag, and cannot delete their own user from the panel. However it is used, the instance keeps at least one working administrator.

## What the panel is not

The panel is web only by design. The JSON API is scoped to a single account, and an instance wide surface has no place in it, so none of these capabilities exist as API endpoints.

The **Support** and **Reviews** areas visible in the panel are placeholders and are not built yet. See [Feature status and roadmap](../troubleshooting/feature-status.md).

## Where to next

- Grant or revoke the flag itself in [Grant instance administrator access](grant-instance-administrator-access.md).
- Understand what account owners can already do without you in [Manage members and roles](../collaboration/manage-members-and-roles.md).
- Review the other operator tools in [Administer with the command line](cli-commands.md).
