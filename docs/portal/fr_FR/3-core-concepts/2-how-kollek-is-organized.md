---
id: kollek.howOrganized
title: Comment KolleK est organisé
slug: organisation-de-kollek
section: concepts-fondamentaux
---

# Comment KolleK est organisé

Cette page vous donne toute la carte avant les détails. Tout le reste de cette section vient ensuite préciser l'une de ses parties.

## L'ossature : quatre niveaux

Tout ce que vous cataloguez dans KolleK vit dans une simple imbrication.

- Un **@doc(accounts.usersAndRoles, "compte")** est votre espace de travail. Tout ce qui suit appartient à exactement un compte.
  - Une **@doc(collections.overview, "collection")** est un groupe nommé d'objets, tel que « Ma collection de comics » ou « Cave à vin ».
    - Un **@doc(items.itemsVsCopies, "objet")** est un type de chose, tel que « Amazing Spider-Man #1 ».
      - Un **@doc(items.itemsVsCopies, "exemplaire")** est une instance physique de cet objet que vous possédez réellement.

Le compte d'Emma contient sa collection « Ma collection de comics ». À l'intérieur se trouve l'objet « Amazing Spider-Man #1 ». Elle en possède deux, donc l'objet compte deux exemplaires, chacun avec son propre état, son propre emplacement de stockage et sa propre valeur.

La distinction entre objet et exemplaire est le cœur du modèle, et elle mérite @doc(items.itemsVsCopies, "sa propre page"). Si vous ne devez lire qu'une seule page de concept, lisez celle-là.

## Les outils partagés

Autour de cette ossature se trouvent quelques outils communs à tout le compte. Ils sont définis une fois et réutilisés partout.

- Les **@doc(collectionTypes.overview)** décident quels détails chaque type d'objet enregistre. Un type Comics demande un numéro d'épisode, un type Vin demande un millésime.
- Les **@doc(organizing.categoriesSetsAndSeries)** regroupent les objets de trois façons différentes : le classement au sein d'une collection, le suivi d'une liste finie jusqu'à sa complétion, et le lien entre une franchise à travers plusieurs collections.
- Les **@doc(tags.overview)** sont des étiquettes libres partagées sur tout le compte, telles que « Dédicacé ».
- Les **@doc(locations.overview)** décrivent où vivent physiquement les exemplaires, et elles s'imbriquent : une boîte sur une étagère dans une pièce.
- Les **@doc(conditions.overview)** évaluent l'état d'un exemplaire, de Neuf à Endommagé.

## La couche d'historique

Chaque exemplaire porte aussi @doc(copyHistory.concept, "son propre historique") : ce que vous avez payé, ce qu'il a valu au fil du temps, l'assurance, les prêts, l'entretien, la provenance, et chaque endroit où il a été rangé. L'exemplaire montre son état actuel, et les enregistrements d'historique racontent l'histoire qui se cache derrière.

## Pour garder les idées claires

:::note
Les détails descriptifs vivent sur l'objet. Tout ce qui est physique (état, emplacement, argent, historique) vit sur l'exemplaire. Dans le doute, demandez-vous : « est-ce vrai pour chaque exemplaire, ou seulement pour celui-ci ? »
:::

## Et ensuite

- Découvrez l'espace de travail et les personnes qui s'y trouvent dans @doc(accounts.usersAndRoles).
- Allez directement à l'idée clé dans @doc(items.itemsVsCopies).
- Vous préférez agir plutôt que lire ? Essayez le @doc(gettingStarted.quickStart, "démarrage rapide en cinq minutes").
