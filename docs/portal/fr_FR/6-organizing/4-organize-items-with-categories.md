---
id: categories.organizeItems
title: Organiser les objets avec des catégories
slug: organize-items-with-categories
section: organizing
---

# Organiser les objets avec des catégories

Une @doc(organizing.categoriesSetsAndSeries, "catégorie") classe les objets à l'intérieur d'une collection, et les catégories peuvent s'imbriquer à n'importe quelle profondeur. Elles répondent à la question "où ceci va t il" une fois qu'une collection dépasse le point où le défilement suffit.

Vous avez besoin du rôle éditeur ou propriétaire pour créer ou modifier des catégories. Tout le monde dans le compte peut les parcourir.

## Quand les catégories aident

La collection "Vinyle" de Noah a dépassé les trois cents disques, et le défilement a cessé de fonctionner. Il crée des catégories pour "Rock" et "Jazz", puis imbrique "Bebop" et "Fusion" sous "Jazz". Chaque disque est classé à un seul endroit précis, et chaque page de catégorie n'affiche que sa propre tranche de la collection.

Les catégories conviennent à la structure interne d'une collection. Si vous voulez une étiquette qui traverse plusieurs collections, utilisez plutôt les @doc(tags.overview, "étiquettes"). Si vous suivez une liste finie à compléter, utilisez un @doc(sets.trackCompletion, "ensemble").

## Créer et imbriquer des catégories

::::steps
:::step title="Ouvrir les catégories de la collection"
Ouvrez la collection et allez dans ses **Catégories**.
:::

:::step title="Créer une catégorie"
Choisissez **Nouvelle catégorie** et donnez lui un nom. Pour l'imbriquer, choisissez une catégorie parente. Noah crée d'abord "Jazz", puis "Bebop" avec "Jazz" comme parent.

::screenshot{label="Formulaire de nouvelle catégorie avec le sélecteur de parent"}
:::

:::step title="Y classer des objets"
Lors de l'ajout ou de la modification d'un objet, choisissez la catégorie sur le formulaire de l'objet. Un objet appartient à au plus une catégorie.
:::
::::

## Parcourir une catégorie

Ouvrir une catégorie affiche la collection filtrée sur cette seule catégorie, avec son propre nombre d'objets et un panneau de statistiques pour cette tranche. C'est le moyen le plus rapide de répondre à "combien de jazz est ce que j'ai vraiment".

## Renommer, déplacer et supprimer

Vous pouvez renommer une catégorie ou la déplacer sous un parent différent à tout moment. Les objets restent classés là où ils sont.

La suppression est une suppression réversible : la catégorie va à la @doc(dataSafety.restoreFromTrash, "corbeille") et peut être restaurée pendant un certain temps.

:::warning
Supprimer une catégorie supprime aussi toutes les catégories imbriquées en dessous. Les objets eux mêmes ne sont jamais supprimés, ils deviennent simplement non catégorisés, mais toute la branche de l'arbre de classement va à la corbeille ensemble.
:::

## Où aller ensuite

- Comparez les trois outils de regroupement dans @doc(organizing.categoriesSetsAndSeries).
- Suivez ce qu'il vous manque encore avec @doc(sets.trackCompletion).
- Récupérez une catégorie supprimée dans @doc(dataSafety.restoreFromTrash).
