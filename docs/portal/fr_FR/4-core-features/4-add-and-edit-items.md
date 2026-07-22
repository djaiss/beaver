---
id: items.addAndEdit
title: Ajouter et modifier des objets
slug: ajouter-et-modifier-des-objets
section: fonctionnalites
---

# Ajouter et modifier des objets

C'est la page pour ce que vous ferez le plus souvent : ajouter des entrées à votre catalogue. Elle parcourt le formulaire d'objet champ par champ, explique quelles parties sont facultatives (presque toutes), et couvre la modification et la suppression.

Si la différence entre un objet et un exemplaire vous semble encore floue, lisez d'abord @doc(items.itemsVsCopies). En résumé : l'objet décrit le type de chose, les exemplaires enregistrent ce que vous possédez physiquement.

## Qui peut faire cela

Ajouter et modifier des objets nécessite le @doc(accounts.usersAndRoles, "rôle") **éditeur** ou **propriétaire**.

## Ajouter un objet

::::steps
:::step title="Ouvrir la collection"
Ouvrez la collection à laquelle appartient l'objet et choisissez **Nouvel objet**.

::screenshot{label="Vue de collection, bouton Nouvel objet"}
:::

:::step title="Nommez le"
Saisissez le **nom**. C'est le seul champ obligatoire. Emma tape « Amazing Spider-Man #300 ». Tout le reste peut être ajouté maintenant ou plus tard.
:::

:::step title="Classez le"
Choisissez éventuellement un **type**, une **catégorie**, un **ensemble** et une **série**, et ajoutez des **étiquettes**. Le type est le plus important : le choisir fait apparaître les champs personnalisés de ce type sur le formulaire.

::screenshot{label="Formulaire d'objet, champs type et classification"}
:::

:::step title="Remplissez les détails"
Remplissez les **champs personnalisés** fournis par le type, téléversez des **photos**, et enregistrez les **exemplaires** que vous possédez, tout cela sur le même formulaire.
:::

:::step title="Enregistrer"
Enregistrez l'objet. Il apparaît immédiatement dans la collection.
:::
::::

## Le formulaire, champ par champ

- **Nom.** Obligatoire, et le seul à l'être.
- **Description.** Texte libre pour tout ce qui ne trouve pas sa place ailleurs.
- **Type.** Quel @doc(collectionTypes.overview, "type de collection") est cet objet. Seuls les types activés sur la collection sont proposés. Le type détermine quels champs personnalisés apparaissent en dessous.
- **Catégorie.** Où l'objet se classe au sein de cette collection. Voir @doc(categories.organizeItems).
- **Ensemble.** Une liste finie que vous êtes en train de compléter. Voir @doc(sets.trackCompletion).
- **Série.** Une franchise qui peut s'étendre sur plusieurs collections. Voir @doc(series.groupFranchise).
- **Étiquettes.** Choisissez des @doc(tags.overview, "étiquettes") existantes ou tapez en une nouvelle et elle est créée sur le champ.
- **Champs personnalisés.** Tout ce que le type choisi définit : texte, nombres, dates, interrupteurs oui ou non, listes de sélection, et notations jusqu'à cinq étoiles. Les champs apparaissent regroupés selon l'organisation du type.
- **Photos.** Couvert en détail dans @doc(items.addPhotos).
- **Exemplaires.** Un ou plusieurs exemplaires physiques, ajoutés directement sur place. Couvert en détail dans @doc(copies.track).

Ne vous sentez pas obligé de tout remplir en une seule fois. Un nom maintenant et les détails plus tard est un flux de travail tout à fait valable, et le même formulaire sert aux deux.

## Modifier un objet

Ouvrez l'objet et choisissez de le modifier. C'est le même formulaire, pré rempli. Changez ce dont vous avez besoin et enregistrez.

## Supprimer un objet

Ouvrez l'objet, choisissez de le supprimer, et confirmez.

:::warning
Supprimer un objet l'envoie, lui et ses exemplaires, à la corbeille. Il est supprimé définitivement après la période de rétention (30 jours par défaut).
:::

Jusque là, vous pouvez le restaurer. Voir @doc(dataSafety.restoreFromTrash).

## Et ensuite

- Enregistrez ce que vous possédez physiquement : @doc(copies.track).
- Rendez le catalogue visuel : @doc(items.addPhotos).
- Commencez à enregistrer l'argent et l'historique : @doc(copyHistory.concept, "L'historique d'un exemplaire expliqué").
