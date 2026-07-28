---
id: kollek.hostingOptions
title: Cloud version versus self hosting
slug: cloud-vs-self-hosting
section: getting-started
---

# Cloud version versus self hosting

Before you invest time in KolleK, it helps to know how you will run it. This page explains your options and, just as important, what you will not be asked to pay for.

## There is no subscription

KolleK has no plans, no tiers, and no paywalled features. Whichever way you run it, you get the same complete application, and nothing is locked behind an upgrade.

There is one difference, and it is about size rather than features. A hosted account holds ten items for free, and stops accepting new ones once it has used that allowance and a small grace on top. Unlocking it is a single payment. A self hosted instance has no item limit at all. See @doc(account.freePlan).

:::note
Self hosting is free and unlimited. Every feature is included no matter how you run it: what a hosted account pays for is the hosting, not the features.
:::

So the choice below is mostly about where the software runs, and about whether you would rather pay for a server or for us to keep one running.

## Option 1: host it yourself

This is the main supported way to run KolleK, and it is free.

You run KolleK on your own server or computer using Docker. Your catalogue, your photos, and your documents all live on infrastructure you control. You decide where backups go, and you can move everything at any time.

Hosting it yourself suits you if you are comfortable running a small web application, or willing to learn. The setup is a short, documented process.

If that describes you, the installation guide will live in the **Self Hosting** section of this documentation. For now, the practical starting point is the project's Docker guide in `docker/README.md`.

## Option 2: use a hosted instance

If you would rather not run anything yourself, someone may offer to host a KolleK instance for you. That is a convenience arrangement handled entirely outside the application. It does not change the software or unlock anything extra. On our own hosted service the account holds ten items for free before it has to be unlocked; another operator may set things up differently.

If you use a hosted instance, you can skip installation entirely and go straight to creating your account.

## Which should you choose

Choose self hosting if you want full control of your data and are happy to run a small service. Choose a hosted instance if you would rather someone else keep the server running. Either way, the app you use is identical, and you can always move your data later, because you can export and back it up.

## Where to next

- Whichever path you took, the next step is the same. @doc(accounts.create).
- Curious what you will be working with first? Read @doc(kollek.whatIs).
