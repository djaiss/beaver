---
id: selfHosting.applicationKeyAndEncryption
title: The application key and encryption
slug: application-key-and-encryption
section: self-hosting
---

# The application key and encryption

This page explains the single most important operational rule of running KolleK. Everything else about the instance is recoverable with patience. This is the one setting that can destroy data irreversibly.

## What the key does

KolleK encrypts sensitive fields at rest with the instance's application key, the `APP_KEY` value in your `.env`. Names, item details, custom field values, file names, email records, webhook secrets: roughly thirty models carry encrypted columns. What lands in the database for those fields is ciphertext, unreadable without the key. The same key also protects user sessions.

This is what @doc(dataSafety.howProtected) describes from the user's point of view. Operationally it means the key is not a configuration detail. It is half of your data.

## The rule

:::warning
Set the application key once, before first boot, and never change it on a running instance. If the key is lost or changed, every encrypted column and every session becomes permanently unreadable. There is no recovery, no support path, and no tool that can bring the data back.
:::

Three practical consequences:

- **Back up the key with the data.** A database backup without its matching key restores to ciphertext. Store the key in a password manager or secrets store, separately from the server.
- **Keep it identical everywhere.** All three application containers (web, queue, scheduler) must run with the same key. The provided Compose file shares one `.env`, which handles this. Preserve that property in any custom deployment.
- **Do not regenerate it "to be safe".** Running `key:generate` against a live instance is the classic self inflicted disaster. The instance refuses to start without a key precisely so nobody boots one accidentally keyless and generates a fresh key mid life.

## Rotating the key deliberately

Some operators must rotate keys on a schedule for policy reasons. KolleK supports this through previous keys: the current `APP_KEY` encrypts everything new, while keys listed in `APP_PREVIOUS_KEYS` (comma separated) can still decrypt existing data.

```bash
APP_KEY=base64:NEW_KEY_HERE
APP_PREVIOUS_KEYS=base64:OLD_KEY_HERE
```

Generate a new key with `php artisan key:generate --show` (never plain `key:generate`, which writes over your live key), move the old key into `APP_PREVIOUS_KEYS`, set the new one as `APP_KEY`, and recreate the containers.

:::warning
Never remove a key from `APP_PREVIOUS_KEYS` while any data it encrypted still exists. Data is only re encrypted with the new key when it is written again, so old records may depend on the old key indefinitely.
:::

If rotation is not required of you, the simplest safe policy is: one key, set once, backed up well.

## Where to next

- Make sure the key is part of your @doc(selfHosting.backupAndRestore, "backup and restore plan").
- Read the user facing view of encryption in @doc(dataSafety.howProtected).
