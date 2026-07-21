---
id: conditions.overview
title: États
slug: etats
section: concepts-fondamentaux
---

# États

Un état évalue l'état physique d'un exemplaire. Cette page explique d'où viennent les états, où vous les rencontrerez, et une limite honnête de la version actuelle.

## Ce qu'est un état

Un état est un grade nommé que vous attribuez à un @doc(items.itemsVsCopies, "exemplaire") : Neuf, Comme neuf, Usé, Abîmé, Endommagé. Chaque compte démarre avec cette liste prête à l'emploi, qui couvre bien la plupart des collections.

Les états appartiennent aux exemplaires, jamais aux objets. Les deux exemplaires d'Emma du même comics sont une pièce Presque neuve sous coque et une autre bien lue, et cette différence est exactement ce que les états capturent.

## Où les états apparaissent

Vous rencontrerez la liste déroulante d'état à trois endroits.

- **Sur l'exemplaire lui-même**, comme son grade actuel, défini lors de @doc(copies.track, "l'enregistrement d'un exemplaire") et mis à jour à chaque changement d'état.
- **Sur les enregistrements d'entretien**, qui capturent l'état avant et après l'intervention, afin qu'une restauration montre ce qu'elle a amélioré. Voir @doc(copies.recordMaintenance).
- **Sur les prêts**, qui capturent l'état au moment où un exemplaire est sorti et au moment où il est revenu, afin que les dommages de transport soient visibles. Voir @doc(loans.lendAndBorrow).

## Évaluer avec cohérence

Les grades sont les vôtres à interpréter, mais ils n'aident que si vous les appliquez toujours de la même façon. Décidez une fois pour toutes ce que « Usé » signifie pour votre passion et tenez-vous-en à cette définition. Si votre communauté a une échelle formelle (les collectionneurs de pièces et leurs grades MS, par exemple), un @doc(collectionTypes.overview, "champ personnalisé") de type Sélection sur le type peut porter le grade formel pendant que l'état porte le vôtre, plus pratique.

## Des états personnalisés, en toute honnêteté

Un compte peut en principe porter sa propre liste d'états au-delà de ceux par défaut.

:::note
Il n'existe actuellement aucun écran dans l'application web pour créer ou renommer des états. Les grades prêts à l'emploi apparaissent comme choix de liste déroulante partout, et en ajouter des vôtres n'est aujourd'hui possible que par l'API. Voir @doc(troubleshooting.featureStatus, "État des fonctionnalités").
:::

## Et ensuite

- Définissez un état sur un exemplaire réel dans @doc(copies.track).
- Voyez les états à l'œuvre dans @doc(copies.recordMaintenance).
- Vérifiez ce qui est prévu dans @doc(troubleshooting.featureStatus, "État des fonctionnalités").
