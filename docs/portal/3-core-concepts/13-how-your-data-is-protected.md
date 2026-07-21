---
id: dataSafety.howProtected
title: How your data is protected
slug: how-your-data-is-protected
section: core-concepts
---

# How your data is protected

A catalogue records what you own, what it is worth, and where it is kept. That is sensitive by nature, and KolleK treats it that way. This page explains the protections in user terms, and is honest about where they end.

## Encrypted at rest

Sensitive fields (names, item details, values, and much more) are encrypted in the database using the instance's encryption key. Someone who obtained a copy of the database file without the key would find the sensitive columns unreadable.

This happens automatically. There is nothing to turn on and nothing to configure as a user.

## Every change is recorded

KolleK keeps an audit trail of user actions. When Sam edits an item, the record shows who did it, what changed, and when, and it feeds the account's activity feed and each item's own log. The actor's name is captured at the time, so history stays readable even if that person's user is later deleted. See [The activity feed and audit trail](../4-core-features/8-activity-feed-and-audit-trail.md).

## The honest boundary

:::note
Encryption at rest protects the stored database contents. It is not end to end encryption. The application can read your data in order to show it to you, and whoever operates the instance holds the encryption key.
:::

In practice this means your trust follows the operator. If you [self host](../14-self-hosting/1-introduction.md), that operator is you, and you hold the key on your own hardware. If someone hosts KolleK for you, they technically hold the key, exactly as with any hosted web application.

Two consequences worth knowing:

- **The key is precious.** If it is lost, the encrypted data cannot be recovered by anyone. Operators should read [The application key and encryption](../14-self-hosting/5-application-key-and-encryption.md).
- **Backups matter.** Encryption protects against snooping, not against loss. Self hosters should follow [Back up and restore your instance](../14-self-hosting/7-back-up-and-restore.md).

## What you control

You choose what leaves the account. Today nothing does: no collection is reachable from outside your account at all. Each collection carries a [visibility setting](12-visibility-and-sharing.md) recording who it is meant for, and when sharing arrives, a collection you marked public will become the only surface a stranger can ever see.

## Where to next

- See who changed what in [The activity feed and audit trail](../4-core-features/8-activity-feed-and-audit-trail.md).
- Harden your own sign in with the [Security overview](../10-security/1-introduction.md).
- Running your own instance? Read [The application key and encryption](../14-self-hosting/5-application-key-and-encryption.md).
