---
id: locations.overview
title: Emplacements
slug: emplacements
section: concepts-fondamentaux
---

# Emplacements

Un emplacement répond à la question que toute collection grandissante finit par poser : « où l'ai-je rangé ? » Cette page explique comment KolleK modélise le stockage physique.

## Ce qu'est un emplacement

Un emplacement est un endroit où un @doc(items.itemsVsCopies, "exemplaire") vit physiquement : une pièce, une étagère, une boîte, un coffre-fort. Chaque emplacement peut porter un emoji afin de se reconnaître d'un coup d'œil dans les listes.

Les emplacements s'imbriquent aussi profondément que nécessaire, afin de refléter votre espace réel. Noah modélise le sien ainsi : Salon, puis Étagère A à l'intérieur, puis Caisse 3 à l'intérieur de celle-ci. Quand il se demande où se trouve un disque, la réponse est aussi précise que sa carte.

Les emplacements sont partagés sur tout le compte. Définissez « Vitrine » une fois et chaque collection peut y stocker des exemplaires, ce qui correspond à la réalité : une même étagère peut accueillir des comics et des pièces de monnaie côte à côte.

## Les emplacements s'attachent aux exemplaires, pas aux objets

Un objet est une idée, c'est donc l'exemplaire qui se trouve quelque part. Les deux exemplaires d'Emma du même comics vivent à des endroits différents : l'un dans la Boîte longue 1, l'autre encadré au mur. Chaque exemplaire pointe vers son propre emplacement actuel.

Un compte fraîchement créé arrive avec quelques emplacements de départ (Salon, Stockage, Vitrine, Garage, Bureau). Renommez-les, imbriquez sous eux, ou remplacez-les par les vôtres.

## Les déplacements sont mémorisés

Lorsque vous déplacez un exemplaire, KolleK ne se contente pas d'écraser l'ancien emplacement. Il enregistre le déplacement, afin que l'exemplaire garde une trace de tous les endroits où il est passé, et quand. L'emplacement actuel n'est que la dernière entrée de cette trace. Cela fait partie de @doc(copyHistory.concept, "l'historique d'un exemplaire"), et le mode d'emploi se trouve dans @doc(copies.move).

## Et ensuite

- Construisez votre carte de stockage dans @doc(locations.setup).
- Déplacez les choses correctement dans @doc(copies.move).
- Découvrez où les emplacements apparaissent sur le formulaire de l'exemplaire dans @doc(copies.track).
