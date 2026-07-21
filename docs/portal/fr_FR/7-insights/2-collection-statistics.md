---
id: insights.collectionStatistics
title: Comprendre les statistiques de votre collection
slug: collection-statistics
section: insights
---

# Comprendre les statistiques de votre collection

Chaque collection possède un écran de statistiques qui transforme votre saisie de données en réponses : que vaut-elle, comment a-t-elle évolué et où se situe la valeur. Cette page explique chaque chiffre et, tout aussi important, d'où il provient, afin que vous puissiez faire confiance à ce que vous lisez.

## D'où viennent les chiffres

Deux règles gouvernent presque tout sur cet écran. Elles découlent de @doc(copyHistory.concept, "le fonctionnement de l'historique d'un exemplaire").

- **La valeur actuelle d'un exemplaire est sa @doc(copies.recordPaymentsAndValue, "évaluation") la plus récente.** Un exemplaire qui n'a jamais été évalué compte comme non évalué, et non comme une valeur nulle à deviner.
- **La date d'acquisition d'un exemplaire provient de sa plus ancienne @doc(copies.recordPaymentsAndValue, "transaction") d'acquisition**, comme un achat, un échange, un don reçu ou un héritage. Un exemplaire sans une telle transaction n'a pas de date d'acquisition, il ne peut donc pas apparaître sur les graphiques temporels. L'écran vous indique combien d'exemplaires ne sont pas datés, afin que vous sachiez ce qui manque aux graphiques.

Si un graphique semble plus vide que ne l'est votre collection, c'est que les statistiques vous invitent à saisir davantage de données, ce n'est pas un bogue.

## Les totaux

En haut : le **nombre d'objets**, le **nombre d'exemplaires**, la **valeur estimée totale** (la somme de la valeur actuelle de chaque exemplaire) et la **valeur moyenne par objet**. Vous verrez aussi ce qui a changé récemment : les objets ajoutés ce mois ci et la valeur ajoutée ce mois ci.

## Complétion des séries

Si la collection contient des @doc(sets.trackCompletion, "séries avec un objectif à atteindre"), l'écran les regroupe : combien de pièces vous possédez par rapport à l'objectif combiné, et le pourcentage de complétion. Seules les séries dont l'objectif est supérieur à zéro y participent. Une série dépassant son objectif compte comme complète, pas comme sur complète.

## Valeur dans le temps

Un graphique sur douze mois de la valeur estimée cumulée de votre collection, mois par mois. Chaque exemplaire rejoint la courbe à sa date d'acquisition, à sa valeur actuelle. Tout ce qui a été acquis avant la fenêtre de douze mois est déjà inclus dans le premier point, de sorte que la courbe part de votre total réel, et non de zéro.

## Acquisitions par mois

Combien d'exemplaires vous avez acquis au cours de chacun des douze derniers mois, calculé à partir des mêmes dates d'acquisition. Un graphique peu fourni ici signifie généralement des transactions d'acquisition manquantes plutôt qu'une année calme.

## Répartitions

- **Par catégorie.** Comment les objets se répartissent entre vos @doc(categories.organizeItems, "catégories"). Les six catégories les plus importantes sont nommées, le reste est regroupé dans « Autre », et les objets non catégorisés apparaissent dans leur propre part.
- **Par état.** Comment vos exemplaires sont notés, en nombre et en pourcentage par @doc(conditions.overview, "état").
- **Valeur par emplacement.** La valeur cumulée des exemplaires à chaque @doc(locations.overview, "emplacement"), pour savoir ce qui se trouve où. Priya utilise cela pour voir combien de valeur se trouve dans sa vitrine par rapport à son coffre. Seuls les emplacements contenant de la valeur apparaissent.

## Meilleurs objets

Les cinq objets les plus précieux de la collection, classés selon la valeur actuelle combinée de leurs exemplaires, chacun affiché avec l'état et l'emplacement de son exemplaire le plus précieux.

## Et ensuite

- Alimentez les graphiques : @doc(copies.recordPaymentsAndValue).
- Suivez correctement la complétion : @doc(sets.trackCompletion).
- Consultez la vue d'ensemble du compte : @doc(insights.dashboard).
