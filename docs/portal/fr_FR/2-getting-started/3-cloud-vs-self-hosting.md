---
id: kollek.hostingOptions
title: Version cloud ou auto hébergement
slug: cloud-ou-auto-hebergement
section: demarrage
---

# Version cloud ou auto hébergement

Avant d'investir du temps dans KolleK, il est utile de savoir comment vous allez le faire fonctionner. Cette page explique vos options et, tout aussi important, ce que vous n'aurez pas à payer.

## Il n'y a pas d'abonnement

KolleK n'a ni offre, ni palier, ni fonctionnalité payante. Quelle que soit la manière dont vous le faites fonctionner, vous obtenez la même application complète, et rien n'est verrouillé derrière une mise à niveau.

Il y a une différence, et elle porte sur la taille plutôt que sur les fonctionnalités. Un compte hébergé contient dix objets gratuitement, puis cesse d'en accepter de nouveaux une fois cette réserve et une petite tolérance épuisées. Le déverrouiller demande un paiement unique. Une instance auto hébergée n'a aucune limite d'objets. Voir @doc(account.freePlan).

:::note
L'auto hébergement est gratuit et illimité. Chaque fonctionnalité est incluse quelle que soit la manière dont vous l'utilisez : ce qu'un compte hébergé paie, c'est l'hébergement, pas les fonctionnalités.
:::

Le choix ci dessous concerne donc surtout l'endroit où le logiciel s'exécute, et le fait de préférer payer un serveur ou nous payer pour en maintenir un.

## Option 1 : l'héberger vous même

C'est la principale méthode prise en charge pour faire fonctionner KolleK, et elle est gratuite.

Vous faites fonctionner KolleK sur votre propre serveur ou ordinateur avec Docker. Votre catalogue, vos photos et vos documents résident tous sur une infrastructure que vous contrôlez. Vous décidez où vont les sauvegardes, et vous pouvez tout déplacer à tout moment.

L'auto hébergement vous convient si vous êtes à l'aise avec l'exécution d'une petite application web, ou prêt à l'apprendre. La mise en place est un processus court et documenté.

Si cela vous correspond, le guide d'installation se trouvera dans la section **Auto hébergement** de cette documentation. Pour l'instant, le point de départ pratique est le guide Docker du projet, dans `docker/README.md`.

## Option 2 : utiliser une instance hébergée

Si vous préférez ne rien faire fonctionner vous même, quelqu'un peut proposer d'héberger une instance KolleK pour vous. C'est un arrangement de convenance géré entièrement en dehors de l'application. Cela ne change pas le logiciel et ne débloque rien de plus. Sur notre propre service hébergé, le compte contient dix objets gratuitement avant de devoir être déverrouillé ; un autre opérateur peut organiser les choses différemment.

Si vous utilisez une instance hébergée, vous pouvez sauter entièrement l'installation et passer directement à la création de votre compte.

## Que choisir

Choisissez l'auto hébergement si vous voulez garder le contrôle total de vos données et que vous êtes heureux de faire fonctionner un petit service. Choisissez une instance hébergée si vous préférez que quelqu'un d'autre s'occupe de faire tourner le serveur. Dans les deux cas, l'application que vous utilisez est identique, et vous pouvez toujours déplacer vos données plus tard, puisque vous pouvez les exporter et les sauvegarder.

## Et ensuite

- Quel que soit le chemin choisi, l'étape suivante est la même. @doc(accounts.create).
- Curieux de savoir avec quoi vous allez travailler en premier ? Lisez @doc(kollek.whatIs).
