---
id: collections.manage
title: Créer et gérer des collections
slug: creer-et-gerer-des-collections
section: fonctionnalites
---

# Créer et gérer des collections

Une @doc(collections.overview, "collection") est le conteneur dans lequel tout le reste vit, c'est donc généralement la première chose que vous créez. Cette page couvre sa création, chaque choix du formulaire, sa modification ultérieure, et ce qui se passe réellement lorsque vous en supprimez une.

## Qui peut faire cela

Créer, modifier et supprimer des collections nécessite le @doc(accounts.usersAndRoles, "rôle") **éditeur** ou **propriétaire**. Les lecteurs peuvent parcourir les collections mais ne peuvent ni les créer ni les modifier.

## Créer une collection

Noah démarre un catalogue pour ses vinyles. Voici ce qu'il fait.

::::steps
:::step title="Démarrer une nouvelle collection"
Depuis l'écran des collections, choisissez **Nouvelle collection**.

::screenshot{label="Écran des collections, bouton Nouvelle collection"}
:::

:::step title="Nommez-la et décrivez-la"
Donnez lui un **nom**, tel que « Disques vinyles », et optionnellement une courte **description** et un **emoji** pour qu'elle se distingue dans les listes.
:::

:::step title="Choisissez ses types de collection"
Choisissez quels @doc(collectionTypes.overview, "types de collection") s'appliquent. Noah choisit le type prêt à l'emploi **Disques vinyles** afin que ses objets aient des champs comme Artiste, Album et Pressage. Vous pouvez activer plusieurs types, ou aucun, et changer cela plus tard.
:::

:::step title="Définissez la devise et la visibilité"
Choisissez la **devise** des valeurs de cette collection, et sa **visibilité**. En cas de doute, gardez les valeurs par défaut. Privée est le point de départ le plus sûr.

::screenshot{label="Formulaire de collection, champs devise et visibilité"}
:::

:::step title="Enregistrer"
Enregistrez la collection. Elle apparaît dans votre liste, vide et prête pour son premier objet.
:::
::::

## Chaque champ, expliqué

- **Nom.** La façon dont la collection apparaît partout. Obligatoire.
- **Description.** Une phrase sur ce qu'elle contient. Facultatif, mais utile une fois que vous avez de nombreuses collections.
- **Emoji.** Un marqueur visuel choisi parmi une palette fixe de douze (📦 📚 💿 🃏 🍷 🎮 🧸 🪙 🖼️ ⌚ 👟 📷). Facultatif.
- **Types de collection.** Les types que vous activez déterminent quels champs personnalisés les objets de cette collection peuvent enregistrer. Vous pouvez en activer plusieurs, par exemple Bandes dessinées et Livres dans une seule collection « Lecture ».
- **Devise.** Chaque montant d'argent de cette collection (valeurs, statistiques) utilise cette devise. Dix huit devises sont disponibles. Elle peut différer de la devise par défaut de votre compte, ce qui est pratique si, par exemple, vous achetez votre vin en euros mais tout le reste en dollars.
- **Visibilité.** À qui la collection est destinée : **privée** (vous seul), **partagée** (tout le monde dans votre compte), ou **publique** (toute personne disposant du lien, en lecture seule). Le réglage est enregistré dès aujourd'hui et sera appliqué une fois le partage disponible. La page de concept @doc(sharing.overview, "visibilité et partage") explique le modèle et son état actuel, et @doc(collections.share) détaille comment le modifier.

## Modifier une collection

Ouvrez la collection et choisissez de la modifier. Le même formulaire apparaît avec les mêmes champs, et vous pouvez en changer n'importe lequel à tout moment. Renommer une collection ou changer son emoji n'affecte rien à l'intérieur.

## Supprimer une collection

Ouvrez la collection, choisissez de la supprimer, et confirmez.

:::warning
Supprimer une collection envoie aussi tous les objets qu'elle contient à la corbeille. La collection et ses objets restent dans la corbeille pendant une durée limitée (30 jours par défaut), puis sont supprimés définitivement.
:::

Tant qu'elle est dans la corbeille, vous pouvez encore changer d'avis. Voir @doc(dataSafety.restoreFromTrash).

## Et ensuite

- Mettez y quelque chose : @doc(items.addAndEdit).
- Choisissez la disposition qui lui convient : @doc(collections.chooseView).
- Montrez la à quelqu'un : @doc(collections.share).
