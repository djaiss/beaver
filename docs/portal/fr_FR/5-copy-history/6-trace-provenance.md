---
id: copies.traceProvenance
title: Retracer la provenance d'un exemplaire
slug: provenance
section: historique
---

# Retracer la provenance d'un exemplaire

La provenance est l'histoire de l'origine d'un exemplaire : qui l'a possédé, où il a été exposé, quand il a été authentifié, et comment il est arrivé jusqu'à vous. Pour les pièces précieuses ou historiquement intéressantes, cette histoire fait partie de la valeur. KolleK vous permet de la construire comme une séquence d'événements de provenance datés, qui se lit, du plus ancien au plus récent, comme un récit.

Contrairement aux autres enregistrements de cette section, la provenance remonte souvent bien avant que vous ne possédiez l'exemplaire, dans des décennies que vous connaissez à peine. Le modèle est conçu pour cette incertitude.

## Ce qu'enregistre un événement de provenance

Chaque événement a un **type**, un **titre**, et autant de contexte que vous en avez : les **parties** impliquées, le **lieu**, une **référence** (un numéro de catalogue, un lot d'enchère, une entrée d'archive), et une **date**.

Les types d'événements couvrent la vie d'un objet : **Acquisition**, **Vente**, **Don**, **Héritage**, **Transfert de propriété**, **Transfert de garde**, **Prêt**, **Retour**, **Exposition**, **Authentification**, **Expertise**, **Restauration importante**, **Origine**, **Découverte**, et **Autre**.

Deux d'entre eux ancrent les extrémités du récit. **Origine** enregistre où l'objet a commencé (sa fabrication, son impression, sa frappe). **Découverte** enregistre le moment où il a refait surface, quand cela constitue une histoire en soi.

## Des dates dont vous n'êtes pas sûr

Les dates de provenance sont souvent approximatives, et prétendre le contraire corromprait le récit. Chaque événement porte une **précision de date** à côté de sa date :

- **Date exacte**. Vous connaissez le jour.
- **Mois**. Vous connaissez le mois et l'année.
- **Année**. Vous connaissez seulement l'année.
- **Approximative**. Une meilleure estimation. À lire comme un « circa ».
- **Inconnue**. L'événement a eu lieu, mais vous ne pouvez pas le dater.

L'événement s'affiche selon sa précision, de sorte que « circa 1970 » et « mars 1970 » paraissent aussi certains qu'ils le sont réellement.

## La règle de l'argent

:::note
Les événements de provenance ne portent aucun montant. L'argent vit toujours dans les transactions. Un événement lié à un achat ou une vente se rattache à sa transaction à la place, afin que le récit et la comptabilité ne divergent jamais.
:::

C'est la même règle que vous avez rencontrée dans @doc(copies.recordPaymentsAndValue), appliquée de l'autre côté.

## Construire un récit de provenance

L'Omega Speedmaster de 1968 de Priya est arrivée avec un dossier de documents de la maison de vente aux enchères. Elle reconstitue son histoire.

::::steps
:::step title="Ouvrez l'historique de l'exemplaire"
Ouvrez l'objet, allez dans son onglet **Historique**, sélectionnez l'exemplaire, et ouvrez la section **Provenance**.

::screenshot{label="Onglet Historique avec la section Provenance ouverte"}
:::

:::step title="Commencez par l'origine"
Ajoutez un événement **Origine** : « Fabriquée, Bienne, Suisse », daté 1968 avec une précision **Année**.
:::

:::step title="Ajoutez ce que les documents confirment"
Ajoutez un **Transfert de propriété** pour le premier propriétaire connu, daté **Approximative** au début des années 1970, avec le nom de la partie tiré des papiers de service. Ajoutez un événement **Authentification** pour l'extrait des archives du fabricant, avec le numéro d'extrait comme **référence**.
:::

:::step title="Terminez par votre acquisition"
Ajoutez un événement **Acquisition** pour son propre achat, daté exactement, et liez-le à la transaction d'achat déjà enregistrée. Le prix vit sur la transaction, pas ici.
:::
::::

Lue de haut en bas, la section raconte désormais l'histoire de la montre, de l'atelier suisse jusqu'à la collection de Priya.

## Vérifié ou légende familiale

Chaque événement porte un indicateur **vérifié** avec une note sur la façon dont il a été vérifié. Utilisez-le honnêtement. Un extrait d'archive est une preuve vérifiée. « Mon grand-père disait toujours qu'il l'avait achetée à Genève » fait aussi partie de l'histoire, mais cela reste non vérifié, et le récit est plus fort d'admettre la différence.

## Des événements qui arrivent d'eux-mêmes

Une partie de la provenance se construit toute seule. Un @doc(loans.lendAndBorrow, "prêt") marqué comme faisant partie de la provenance ajoute des événements de prêt et de retour correspondants, et un @doc(copies.recordMaintenance, "enregistrement de maintenance") signalé comme important apparaît comme un événement de restauration. Vous assemblez le passé lointain, le présent se documente de lui-même à mesure qu'il se produit.

## Où aller ensuite

- Joignez l'extrait d'archive ou le certificat à son événement dans @doc(copies.attachDocuments).
- Enregistrez l'achat auquel se rattache l'événement d'acquisition dans @doc(copies.recordPaymentsAndValue).
- Lisez l'histoire complète dans @doc(copyHistory.readTimeline).
