---
id: copies.move
title: Déplacer un exemplaire et garder son historique de lieux
slug: deplacer-un-exemplaire
section: historique
---

# Déplacer un exemplaire et garder son historique de lieux

Quand vous avez choisi un @doc(locations.overview, "lieu") en créant un exemplaire, vous avez indiqué à KolleK où il vit. Déplacer est différent : cela change où vit l'exemplaire *tout en se souvenant d'où il vivait avant*. Au fil des années, cela devient une trace de tous les endroits où l'exemplaire a été conservé.

## Comment fonctionne l'historique de lieux

L'historique de lieux d'un exemplaire est une chaîne d'enregistrements. À tout moment, exactement un enregistrement est ouvert, et cet enregistrement ouvert est le lieu actuel de l'exemplaire. Enregistrer un déplacement clôt l'enregistrement précédent et en ouvre un nouveau, horodaté avec la date et le motif.

Ainsi, « où est-il » et « où a-t-il été » sont les mêmes données, lues depuis deux extrémités différentes.

## Enregistrer un déplacement

Priya déplace son Omega Speedmaster de 1968 du tiroir de son bureau vers le coffre après sa nouvelle estimation.

::::steps
:::step title="Ouvrez l'historique de l'exemplaire"
Ouvrez l'objet, allez dans son onglet **Historique**, sélectionnez l'exemplaire, et ouvrez la section **Lieux**.

::screenshot{label="Onglet Historique avec la section Lieux ouverte"}
:::

:::step title="Choisissez la destination"
Enregistrez un déplacement et choisissez le **nouveau lieu** parmi les lieux de votre compte. Si l'endroit voulu n'existe pas encore, un propriétaire ou un éditeur peut d'abord l'ajouter (voir @doc(locations.setup)).
:::

:::step title="Indiquez quand et pourquoi"
Définissez la **date** du déplacement, un **motif** (« Valeur plus élevée, déplacé vers le coffre »), et une **note** éventuelle. Enregistrez.
:::
::::

L'exemplaire affiche désormais le coffre comme son lieu actuel, et le tiroir est devenu de l'historique avec une date de début, une date de fin, et un motif de départ.

## Corriger une erreur

Corriger un enregistrement passé est possible. Si vous avez mal saisi une date ou choisi la mauvaise étagère, modifiez l'enregistrement plutôt que d'ajouter un faux déplacement pour compenser. L'historique doit refléter ce qui s'est réellement passé.

:::note
Déplacer sert aux relocalisations physiques réelles. Si vous réorganisez votre stockage lui-même, en renommant une étagère ou en imbriquant une boîte ailleurs, changez plutôt le lieu dans @doc(locations.setup). Les exemplaires continuent d'y pointer, et aucun enregistrement de déplacement n'est nécessaire.
:::

## Pourquoi se donner la peine avec l'historique

Pour les exemplaires du quotidien, le lieu actuel est tout ce que vous vérifierez jamais. L'historique montre son utilité avec les exemplaires précieux : un assureur peut demander où une pièce a été conservée, un acheteur peut se soucier qu'elle ait passé une décennie dans une vitrine climatisée, et vous pouvez simplement vouloir savoir où se trouvait quelque chose l'année où il a été rayé. Comme les déplacements sont datés, l'historique de lieux s'aligne avec les prêts, la maintenance, et les changements d'état dans la @doc(copyHistory.readTimeline, "chronologie").

## Où aller ensuite

- Construisez une carte de stockage qui vaut la peine d'être consultée dans @doc(locations.setup).
- Un exemplaire qui quitte vos mains est un prêt, pas un déplacement. Voir @doc(loans.lendAndBorrow).
- Voyez les déplacements dans l'histoire complète dans @doc(copyHistory.readTimeline).
