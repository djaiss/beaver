---
id: selfHosting.index
title: Self hosting overview
slug: self-hosting
section: self-hosting
---

# Self hosting overview

Running your own KolleK instance is a first class, supported way to use the product, and it is free. This page tells you what you are signing up for before you install anything, and hands you the one rule that matters more than all the others.

If you have not yet decided between self hosting and a hosted instance, start with [Cloud version versus self hosting](../2-getting-started/3-cloud-vs-self-hosting.md).

## What running an instance involves

KolleK ships as a single Docker image that plays three roles, selected by an environment variable:

- The **web** role serves the application itself.
- The **queue** role works through background jobs (sending email, webhook deliveries, logging).
- The **scheduler** role runs the daily maintenance jobs.

The provided Docker Compose file starts all three, plus a MySQL database. Sessions, cache, and the queue are all database backed, so there is no Redis or other extra service to operate. Uploaded photos and documents live on a storage volume, on local disk by default, with S3 compatible storage available.

The requirements are modest: a machine with Docker Engine 24 or newer and the Compose plugin. A small virtual server comfortably runs a personal instance.

## The one rule to internalize now

KolleK encrypts sensitive data at rest using your instance's application key.

:::warning
Set the application key once, before first boot, and never change it on a running instance. If the key changes, every encrypted field and every session becomes permanently unreadable. Treat the key like the data itself: back it up, and keep it identical across all containers.
:::

This is worth reading about properly before you install. [The application key and encryption](5-application-key-and-encryption.md) explains what the key protects, how to store it, and the one safe way to rotate it deliberately.

## Your responsibilities

Self hosting means you are the operator. Concretely, that is:

- **Installation and upgrades.** Both are short, documented Docker procedures.
- **Backups.** There is no automated in app backup. You back up the database and the storage volume yourself, along with the application key.
- **Email delivery.** A fresh instance logs email instead of sending it, so invitations and sign in links do not go anywhere until you configure a mailer.
- **Keeping the three roles running.** In particular, background jobs and daily maintenance silently stop if the queue or scheduler containers are down.

Alex, who runs an instance for their collectors club, spends a few minutes on this per month once the initial setup is done. It is not a heavy operational burden, but it is yours.

## This section

Work through the pages in roughly this order:

1. [Install with Docker](2-install-with-docker.md). From nothing to a running instance.
2. [Configure your instance](3-configure-your-instance.md). The environment variables you will actually touch.
3. [Set up email delivery](4-set-up-email-delivery.md). Make invitations and magic links actually send.
4. [The application key and encryption](5-application-key-and-encryption.md). The most important operational rule.
5. [Upgrade your instance](6-upgrade-your-instance.md). Move to a new version safely.
6. [Back up and restore your instance](7-back-up-and-restore.md). Protect the data.
7. [Scheduled maintenance jobs](8-scheduled-jobs.md). What the app does on its own every night.
8. [Grant instance administrator access](9-grant-instance-administrator-access.md). Bootstrap the server wide administrator.
9. [The instance administration panel](10-instance-administration-panel.md). What that administrator can see and do.
10. [Administer with the command line](11-cli-commands.md). The artisan commands an operator needs.
11. [Add a language](12-add-a-language.md). How the interface is translated.

## Where to next

- Ready to install? Go to [Install with Docker](2-install-with-docker.md).
- Prefer a guided end to end walkthrough? Follow the [self hosting tutorial](../12-tutorials/6-self-host-with-docker.md).
