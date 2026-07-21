---
id: copyHistory.concept
title: L'historique d'un exemplaire
slug: copy-history
section: core-concepts
---

# L'historique d'un exemplaire

Cette page explique le cœur conceptuel de KolleK : un exemplaire affiche son état actuel, tandis que tout ce qui lui est jamais arrivé vit dans des enregistrements séparés et datés. Comprenez cela une fois, et toute la section Suivi devient un ensemble de tâches évidentes.

## État actuel contre historique

Regardez l'une des montres de Priya. L'exemplaire vous indique son état actuel d'un coup d'œil : son @doc(conditions.overview, "état") est Usé, son @doc(locations.overview, "emplacement") actuel est la vitrine, sa valeur estimée est celle indiquée par la dernière expertise.

Rien de tout cela n'est saisi comme un simple fait qui écrase le précédent. Chacun est la partie visible d'un enregistrement sous-jacent.

- La valeur estimée est sa **valorisation la plus récente**.
- Le prix qu'elle a payé, et la date d'acquisition, proviennent de sa **plus ancienne transaction d'acquisition**.
- L'emplacement actuel est l'**entrée ouverte de son historique d'emplacement**.

L'exemplaire est un résumé. Les enregistrements sont la vérité.

## Les types d'enregistrements

Sept types d'enregistrements datés peuvent être rattachés à un exemplaire, chacun avec son propre objectif et sa propre page de mode d'emploi.

- Les **transactions** enregistrent l'argent et les changements de propriété : ce que vous avez payé, ce pour quoi vous avez vendu, les frais, l'expédition. Voir @doc(copies.recordPaymentsAndValue).
- Les **valorisations** enregistrent ce que l'exemplaire valait à un instant donné, et qui l'a estimé. Même page que les transactions, car les deux sont faciles à confondre.
- Les **enregistrements d'assurance** capturent la couverture : assureur, valeur assurée, dates de la police. Voir @doc(copies.insure).
- Les **prêts** suivent la garde lorsqu'un exemplaire quitte vos mains ou arrive de chez quelqu'un d'autre. Voir @doc(loans.lendAndBorrow).
- Les **enregistrements d'entretien** consignent le nettoyage, la réparation, et les travaux de conservation. Voir @doc(copies.recordMaintenance).
- Les **événements de provenance** construisent l'histoire de propriété et d'authenticité. Voir @doc(copies.traceProvenance).
- L'**historique d'emplacement** mémorise chaque endroit où l'exemplaire a vécu. Voir @doc(copies.move, "Déplacer un exemplaire").

Vous pouvez aussi @doc(copies.attachDocuments, "joindre des documents") (reçus, expertises, certificats) à l'exemplaire ou à n'importe quel enregistrement individuel, et tout lire fusionné sur @doc(copyHistory.readTimeline, "la chronologie de l'exemplaire").

## Les deux règles qui gardent tout cohérent

**L'argent ne vit jamais que dans les transactions.** Un prix d'achat est une transaction. Une vente est une transaction. Les valorisations et les événements de provenance décrivent la valeur et l'histoire, jamais le paiement.

**L'historique n'est qu'un ajout.** Réévaluer un exemplaire écrit une nouvelle valorisation à côté de l'ancienne. Renouveler une assurance écrit un nouvel enregistrement. Rien n'écrase le passé, c'est pourquoi la chronologie peut raconter toute l'histoire des années plus tard.

:::note
Si vous vous surprenez à modifier un ancien enregistrement pour refléter quelque chose de nouveau, arrêtez-vous et ajoutez plutôt un nouvel enregistrement. La modification sert à corriger des erreurs, pas à mettre la réalité à jour.
:::

## Avez-vous besoin de tout cela ?

Non. Emma cataloge la plupart de ses comics avec seulement un exemplaire, un état, et un emplacement. Les enregistrements d'historique se justifient sur les pièces qui comptent : les précieuses, les assurées, les prêtées, et les héritées. Utilisez-en autant ou aussi peu que chaque exemplaire le mérite.

## Et ensuite

- Commencez par l'argent dans @doc(copies.recordPaymentsAndValue).
- Voyez toute l'histoire en une seule vue dans @doc(copyHistory.readTimeline).
- Suivez une pièce précieuse de bout en bout dans le tutoriel @doc(tutorials.trackValuableItem, "Suivre toute la vie d'un objet de valeur").
