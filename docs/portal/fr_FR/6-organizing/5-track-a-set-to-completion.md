---
id: sets.trackCompletion
title: Suivre un ensemble jusqu'à sa complétion
slug: track-a-set-to-completion
section: organizing
---

# Suivre un ensemble jusqu'à sa complétion

Un @doc(organizing.categoriesSetsAndSeries, "ensemble") est une liste finie que vous cherchez à compléter à l'intérieur d'une collection : une série limitée de douze épisodes, une équipe complète de cartes de recrues, une discographie entière. Donnez à un ensemble un nombre cible et KolleK vous montre ce que vous possédez par rapport à ce nombre cible, de sorte que "à quel point suis je proche" a toujours une réponse.

Vous avez besoin du rôle éditeur ou propriétaire pour créer ou modifier des ensembles.

## Créer un ensemble

::::steps
:::step title="Ouvrir les ensembles de la collection"
Ouvrez la collection et allez dans ses **Ensembles**.
:::

:::step title="Créer l'ensemble"
Choisissez **Nouvel ensemble**, nommez le, et saisissez le **nombre cible**, le nombre d'objets que contient l'ensemble complet. Noah crée "Série Blue Note 1500" avec une cible de 100.

::screenshot{label="Formulaire de nouvel ensemble avec le champ nombre cible"}
:::

:::step title="Y ajouter des objets"
Lors de l'ajout ou de la modification d'un objet, choisissez l'ensemble sur le formulaire de l'objet. Chaque objet que vous classez dans l'ensemble fait monter le nombre possédé.
:::
::::

## Comment la complétion est comptée

La page de l'ensemble récapitule ce que vous possédez par rapport à la cible. Posséder 37 disques d'une série de 100 se lit comme 37 sur 100. La complétion alimente aussi les @doc(insights.collectionStatistics, "statistiques de collection"), où tous vos ensembles se combinent en un chiffre de complétion global.

Deux détails à connaître.

- **Seul un ensemble avec une cible supérieure à zéro compte dans les statistiques de complétion.** Un ensemble sans cible regroupe quand même des objets, il ne fait simplement aucune affirmation de complétion.
- **Un ensemble contenant plus d'objets que sa cible compte comme complet, pas comme surcomplet.** Les doublons et les variantes ne vous feront pas dépasser 100 pour cent.

## Ensembles contre séries

Un ensemble vit à l'intérieur d'une collection et suit une complétion. Une @doc(series.groupFranchise, "série") s'étend sur plusieurs collections et ne suit rien du tout, elle ne fait que regrouper une franchise. Si vous vous demandez "lesquels me manque t il", vous voulez un ensemble.

## Supprimer un ensemble

Supprimer un ensemble l'envoie à la @doc(dataSafety.restoreFromTrash, "corbeille"), et il peut y être restauré.

:::warning
Supprimer un ensemble retire le regroupement, et son suivi de complétion avec lui. Les objets de l'ensemble ne sont pas supprimés et conservent toutes leurs données.
:::

## Où aller ensuite

- Regroupez une franchise à travers les collections avec les @doc(series.groupFranchise, "séries").
- Voyez la complétion se récapituler dans @doc(insights.collectionStatistics).
