---
id: instanceAdmin.panel
title: The instance administration panel
slug: instance-administration-panel
section: self-hosting
---

# The instance administration panel

The instance administration panel at `/instance-admin` is where an @doc(instanceAdmin.grantAccess, "instance administrator") looks across every account on the server: how many there are, who is in them, and the handful of destructive actions only an operator should hold. This page describes what the panel can do, and just as important, what it deliberately cannot.

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

## Site options

The **Site options** area holds the settings for the public marketing site, the pages a visitor sees before signing in. That site is off by default on a self hosted instance (see @doc(selfHosting.configure)), so if you never turned it on, nothing here changes what anyone sees.

### The announcement banner

The banner is the black bar across the top of every marketing page. It is the place for one short sentence: a release you want people to notice, a maintenance window, an event.

Only the sentence is needed. Everything else is optional:

- **Show the banner** switches it on and off. Set it to **No** and no bar appears, whatever else you have filled in.
- **Version** is the small green pill on the left, such as `v0.9`. Leave it empty and the pill goes away.
- **Link** is the address the banner points to, and **Link label** is the text a visitor clicks. Leave the link empty for a banner that only says something.
- **Sentence** is the announcement itself.

The marketing site is served in several languages, so the sentence and the link label are written one language at a time, with a tab for each. A language you leave empty falls back to English, which means filling in English alone already gives every visitor a banner. The green dot on a tab tells you that language has a sentence of its own.

The preview above the form shows the bar the way a visitor will see it, in the language of the tab you are on. Saving clears the cached marketing pages for you, so the change is live straight away.

### Clearing the response cache

Marketing pages change rarely, so each one is rendered once and then served from a cache for seven days. That keeps the public site fast, but it also means an edit can sit unseen for a week.

**Clear cache** drops every cached page at once. Reach for it after changing something the public site shows that the application does not know about, such as a documentation page you edited on the server. Saving the banner and moderating a testimonial already clear the cache themselves.

Clearing loses nothing. Each page is rendered again the next time somebody asks for it, and the only cost is that the first visitor waits for that render. The same thing can be done from the command line with `php artisan responsecache:clear`, described in @doc(selfHosting.cliCommands).

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

The **Support** and **Reviews** areas visible in the panel are placeholders and are not built yet. See @doc(troubleshooting.featureStatus).

## Where to next

- Grant or revoke the flag itself in @doc(instanceAdmin.grantAccess).
- Understand what account owners can already do without you in @doc(collaboration.manageMembersAndRoles).
- Review the other operator tools in @doc(selfHosting.cliCommands).
