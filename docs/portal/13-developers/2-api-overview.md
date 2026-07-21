---
id: api.overview
title: API overview
slug: api-overview
section: developers
---

# API overview

The KolleK API is a JSON API that mirrors the web application one to one. Every capability of the app (creating collections, adding items and copies, recording transactions, managing members) has a matching endpoint, enforced by exactly the same rules. If your role lets you do something in the browser, your token lets you do it over HTTP. If it does not, the API refuses in the same way the app would.

This page gives you the mental model. The complete, always current endpoint reference is generated from the code and served by your instance:

- `/docs/api` for the browsable reference.
- `/docs/api.md` for the whole reference as Markdown.
- `/docs/api/{section}.md` for a single section as Markdown, handy for feeding one topic to a tool.

:::note
On a self hosted instance, the reference is part of the public marketing site, which is off by default. An operator turns it on with the `SHOW_MARKETING_SITE` setting. See @doc(selfHosting.configure).
:::

## Scoped to your account

The API is tenant scoped. A token belongs to a user, and a user belongs to exactly one @doc(accounts.usersAndRoles, "account"), so every request resolves through that account. You cannot reach another account's data, and you do not pass an account identifier anywhere. There is nothing to configure: authenticate, and you are inside your own workspace.

The same @doc(accounts.usersAndRoles, "roles") apply as in the app. A viewer's token can read but not write. An editor's token can manage catalogue content. Owner only actions (members, account settings) need an owner's token.

## How the resources are shaped

Resources nest the way @doc(kollek.howOrganized, "KolleK is organized"):

- Your **account** holds account wide resources: members, collection types, custom fields, tags, locations, conditions.
- **Collections** hold **items**, along with categories and sets.
- **Items** hold **photos** and **copies**.
- **Copies** carry the history resources: transactions, valuations, insurance records, loans, maintenance records, provenance events, location history, documents, and the combined timeline.

Responses loosely follow the JSON:API shape: each resource comes back as `type`, `id`, `attributes`, and `links`. Lists are paginated with a standard envelope, covered in @doc(api.rateLimitsAndConventions).

## What this section covers

These pages cover getting started and the concepts that the generated reference cannot teach: authentication, conventions, and the current state of webhooks. For any specific endpoint, its parameters, and worked request and response examples, go straight to `/docs/api`.

:::note
There is no test mode. Every API request runs against your real account, so be careful with destructive calls while you experiment.
:::

## Where to next

- Make your first request in @doc(api.authenticate).
- Skim @doc(api.rateLimitsAndConventions) before writing a client.
- Browse the generated reference at `/docs/api` on your instance.
