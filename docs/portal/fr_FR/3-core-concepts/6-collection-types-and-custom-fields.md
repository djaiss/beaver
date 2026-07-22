---
id: collectionTypes.overview
title: Types de collection et champs personnalisés
slug: types-et-champs-personnalises
section: concepts-fondamentaux
---

# Types de collection et champs personnalisés

Les comics ont besoin d'un numéro d'épisode. Le vin a besoin d'un millésime. Les montres ont besoin d'un mouvement. KolleK ne devine pas ce que vous collectionnez, il vous laisse le définir. Cette page explique les différentes pièces : les types, les champs personnalisés, et les groupes de champs.

## Les types de collection

Un **type de collection** décrit un type d'objet que vous collectionnez : Comics, Vinyles, Vin. C'est le contenant des champs personnalisés qui ont du sens pour ce type d'objet.

Les types sont partagés sur tout le compte et réutilisables. Définissez un type Comics une fois, et n'importe quelle @doc(collections.overview, "collection") de votre compte peut l'activer. Une collection peut activer plusieurs types à la fois, ce qui convient aux collections mixtes : la collection « Musique » de Noah active à la fois Vinyles et CD, afin que chaque objet puisse être catalogué comme l'un ou l'autre.

Lorsqu'un objet reçoit un type, son formulaire s'enrichit des champs personnalisés que ce type définit.

## Les champs personnalisés

Un **champ personnalisé** est un détail qu'un type demande. Chaque champ a son propre type de donnée.

- **Texte**, pour tout ce qui est libre, tel que Éditeur ou Artiste.
- **Nombre**, pour le Numéro d'épisode ou l'Année de sortie.
- **Date**, pour une date de couverture.
- **Oui / Non**, pour Dédicacé ou Première édition.
- **Sélection**, une liste déroulante avec des options que vous définissez, telle qu'une note PSA 10, PSA 9, ou Brut.
- **Note**, jusqu'à cinq étoiles, pour votre « Ma note » personnelle.

Les valeurs sont enregistrées par objet. « Amazing Spider-Man #1 » d'Emma a le Numéro d'épisode 1 et l'Éditeur Marvel ; ses autres comics partagent les mêmes champs avec leurs propres valeurs.

## Les groupes de champs

Lorsqu'un type comporte de nombreux champs, les **groupes de champs** gardent le formulaire lisible. Un groupe n'est qu'une section nommée : le type Comics prêt à l'emploi regroupe ses champs sous « Informations d'édition » et « État et évaluation ». Les formulaires longs se lisent alors comme des sections ordonnées plutôt que comme une liste interminable.

## Les types prêts à l'emploi

Un compte fraîchement créé est livré avec une douzaine de types prêts à l'emploi, afin que vous ne partiez pas d'une page blanche : Comics, Cartes à collectionner, Vinyles, CD, DVD, Pièces de monnaie, Timbres, Livres, Figurines / Jouets, Jeux vidéo, Montres, et Vin, chacun avec des champs déjà sensés et regroupés. Utilisez-les tels quels, ajustez-les, ou ignorez-les et construisez les vôtres.

:::note
Les types décrivent les objets, pas les exemplaires. Un champ qui varie d'une pièce physique possédée à l'autre, tel que l'état ou un numéro de série, appartient plutôt à l'exemplaire. Voir @doc(items.itemsVsCopies).
:::

## Et ensuite

- Construisez ou ajustez un type étape par étape dans @doc(collectionTypes.setup).
- Partagez une définition de type avec quelqu'un dans @doc(collectionTypes.importExport).
- Voyez les champs en action dans @doc(items.addAndEdit).
