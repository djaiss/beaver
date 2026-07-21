---
id: locations.setup
title: Configurer vos emplacements
slug: configurer-vos-emplacements
section: organisation
---

# Configurer vos emplacements

Un @doc(locations.overview, "emplacement") est l'endroit où un exemplaire vit physiquement. Les emplacements s'imbriquent aussi profondément que votre stockage réel, de sorte que "deuxième caisse à gauche, sous la fenêtre" devient quelque chose que vous pouvez réellement enregistrer. Une bonne carte des emplacements est ce qui vous permet de retrouver un disque précis dans une pièce qui en est pleine.

Les emplacements sont partagés à l'échelle du compte : construisez la carte une fois et chaque collection l'utilise. Vous avez besoin du rôle éditeur ou propriétaire pour gérer les emplacements.

## Partir des valeurs par défaut

Un compte tout juste créé arrive avec cinq emplacements de départ : Salon 🛋️, Stockage 📦, Vitrine 🗄️, Garage 🚗 et Bureau 🏢. Renommez les, imbriquez en dessous, ou supprimez les. Ils existent pour que votre premier exemplaire ait un endroit où aller.

## Modéliser votre stockage réel

L'astuce consiste à refléter le monde physique, du général au particulier. Noah range ses vinyles dans deux pièces, et à l'intérieur de chaque pièce il y a des étagères, et sur les étagères des caisses.

- Salon
  - Étagère A
    - Caisse 1
    - Caisse 2
  - Étagère B
- Bureau
  - Placard

::::steps
:::step title="Ouvrir les emplacements"
Allez dans les paramètres du compte et ouvrez **Emplacements**.
:::

:::step title="Créer d'abord les lieux de premier niveau"
Ajoutez les pièces ou les zones, en laissant le parent vide. Donnez à chacun un emoji si vous le souhaitez, cela facilite le parcours des listes.

::screenshot{label="Liste des emplacements avec des entrées imbriquées"}
:::

:::step title="Imbriquer le détail en dessous"
Ajoutez étagères, boîtes, caisses et classeurs, chacun avec son parent défini. Allez aussi profond que votre stockage l'exige réellement, et pas plus.
:::
::::

Ne sur construisez pas. Si tout tient dans un seul placard, un seul emplacement nommé "Placard" est une carte parfaitement adaptée. Ajoutez de la profondeur quand retrouver les choses devient difficile, pas avant.

## Comment les emplacements sont utilisés

Chaque @doc(items.itemsVsCopies, "exemplaire") pointe vers son emplacement actuel, choisi quand vous créez l'exemplaire ou chaque fois que vous le @doc(copies.move, "déplacez"). Les déplacements sont enregistrés dans le temps, de sorte qu'un exemplaire se souvient non seulement d'où il est mais d'où il a été. Les emplacements alimentent aussi la répartition de valeur par emplacement dans les @doc(insights.collectionStatistics, "statistiques de collection"), ce qui vous permet d'apprendre qu'une vitrine détient la moitié de la valeur de votre collection.

## Où aller ensuite

- Mettez la carte au travail dans @doc(copies.track).
- Enregistrez un déplacement correctement dans @doc(copies.move).
