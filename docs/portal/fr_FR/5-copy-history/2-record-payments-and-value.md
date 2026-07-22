---
id: copies.recordPaymentsAndValue
title: Enregistrer ce que vous avez payé et ce que ça vaut
slug: paiements-et-valeur
section: historique
---

# Enregistrer ce que vous avez payé et ce que ça vaut

L'argent et la valeur sont les deux questions que se posent le plus souvent les collectionneurs, et KolleK les garde délibérément séparées. Une **transaction** enregistre de l'argent qui change réellement de mains. Une **estimation** enregistre ce que vaut un exemplaire à un instant donné, que de l'argent ait bougé ou non. Cette page vous montre comment enregistrer les deux, et explique la règle qui les garde distinctes.

Si vous n'avez pas lu @doc(copyHistory.concept, "L'historique d'un exemplaire expliqué"), lisez le d'abord. Il introduit l'idée que ces enregistrements forment un historique en ajout seul, et non des champs que vous écrasez.

## La règle qui garde tout clair

Un prix d'achat est une transaction, pas une estimation.

Quand Priya achète une Omega Speedmaster de 1968 pour 4 200, il s'agit d'une transaction de type **Achat**. Elle enregistre ce qu'elle a payé ce jour-là, et cela ne change jamais. Ce que vaut la montre est une question séparée qui change avec le temps, et chaque réponse est sa propre estimation.

KolleK dérive automatiquement deux chiffres de ces enregistrements :

- La **valeur estimée** d'un exemplaire est le montant de son estimation la plus récente. Un exemplaire sans estimation apparaît comme non estimé, pas comme valant zéro.
- Le **prix payé** et la **date d'acquisition** d'un exemplaire proviennent de sa première transaction d'acquisition (un Achat, un Échange, un Don reçu ou un Héritage).

Vous ne saisissez jamais ces chiffres directement sur l'exemplaire. Vous enregistrez l'historique, et les chiffres actuels en découlent.

## Enregistrer une transaction

Une transaction couvre tout mouvement d'argent ou de propriété autour d'un exemplaire : l'acheter, le vendre, l'échanger, payer des frais, ou l'expédier quelque part.

::::steps
:::step title="Ouvrez l'historique de l'exemplaire"
Ouvrez l'objet, allez dans son onglet **Historique**, et sélectionnez l'exemplaire voulu. Puis ouvrez la section **Transactions**.

::screenshot{label="Onglet Historique avec la section Transactions ouverte"}
:::

:::step title="Ajoutez une transaction"
Choisissez d'ajouter une transaction et sélectionnez son **type** : Achat, Vente, Échange, Don reçu, Don donné, Héritage, Remboursement, Frais, Taxe, Expédition, ou Autre.
:::

:::step title="Saisissez le montant"
Renseignez le **montant**, et facultativement les **taxes**, les **frais** et l'**expédition**, afin que le coût total réel soit capturé, pas seulement le prix affiché.
:::

:::step title="Ajoutez le contexte"
Enregistrez la **contrepartie** (à qui vous avez acheté ou vendu), la **date**, et une **référence** telle qu'un numéro de commande ou de lot d'enchère. Enregistrez la transaction.
:::
::::

Priya enregistre l'achat de sa Speedmaster : type **Achat**, montant 4 200, frais 120 pour la maison de vente aux enchères, contrepartie « Fine Time Auctions », et le numéro de lot en référence. Cet unique enregistrement répond désormais à ce qu'elle a payé, quand elle l'a acquise, et d'où elle vient.

:::note
La première transaction d'acquisition (Achat, Échange, Don reçu ou Héritage) est ce qui donne à l'exemplaire sa date d'acquisition. Les exemplaires qui n'en ont pas sont comptés comme non datés dans vos statistiques, alors enregistrez la même chose pour des objets achetés il y a longtemps, avec votre meilleure estimation de la date.
:::

## Enregistrer une estimation

Une estimation répond à « combien ça vaut maintenant, et avec quel degré de certitude ».

::::steps
:::step title="Ouvrez la section Estimations"
Depuis le même onglet **Historique**, avec votre exemplaire sélectionné, ouvrez la section **Estimations**.
:::

:::step title="Ajoutez une estimation"
Choisissez un **type d'estimation** : Estimation personnelle, Expertise professionnelle, Estimation de marché, Valeur d'assurance, Estimation d'enchère, Estimation automatisée, ou Autre.
:::

:::step title="Saisissez la valeur et votre degré de confiance"
Renseignez le **montant**, choisissez un niveau de **confiance** (Faible, Moyen, Élevé, ou Inconnu), et enregistrez **qui a fait l'estimation**. Enregistrez le tout.

::screenshot{label="Formulaire de nouvelle estimation avec type, montant et confiance"}
:::
::::

Deux ans plus tard, un marchand dit à Priya que la Speedmaster se vendrait autour de 5 500. Elle ajoute une nouvelle estimation : **Estimation de marché**, 5 500, confiance **Moyenne**, estimée par le marchand. Son estimation d'origine reste dans l'historique, et la valeur estimée de l'exemplaire se met à jour avec le nouveau chiffre.

:::note
Réévaluer écrit toujours une nouvelle estimation. Vous ne modifiez jamais l'ancienne pour y mettre un nouveau chiffre, ce qui vous permet de garder un enregistrement authentique de l'évolution de la valeur dans le temps. Cet historique est ce qui trace le graphique de la valeur dans le temps dans vos statistiques.
:::

## Où ces chiffres apparaissent

Les chiffres que vous enregistrez ici alimentent le reste de KolleK : la valeur totale affichée sur chaque collection, les graphiques de valeur dans le temps et d'acquisitions dans les @doc(insights.collectionStatistics, "statistiques de collection"), et les meilleurs objets par valeur. Des transactions et des estimations soignées sont ce qui rend ces écrans fiables.

## Où aller ensuite

- Gardez les documents avec l'enregistrement. @doc(copies.attachDocuments), comme le reçu sur une transaction ou l'expertise sur une estimation.
- Vous assurez l'exemplaire pour cette valeur ? @doc(copies.insure).
- Vous construisez l'histoire complète de la propriété ? @doc(copies.traceProvenance).
