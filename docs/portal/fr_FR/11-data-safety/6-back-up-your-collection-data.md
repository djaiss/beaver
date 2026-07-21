---
id: dataSafety.backupCollectionData
title: Sauvegarder les données de votre collection
slug: sauvegarder-les-donnees-de-collection
section: protection-des-donnees
---

# Sauvegarder les données de votre collection

« Comment récupérer mes données » mérite une réponse claire. Cette page indique précisément ce que KolleK peut exporter depuis l'application aujourd'hui, ce qu'il ne peut pas encore faire, et quel est le véritable chemin de sauvegarde en attendant.

## Ce que vous pouvez exporter aujourd'hui

**Les définitions de types de collection.** Un @doc(collectionTypes.overview, "type de collection") peut être exporté sous forme de fichier JSON (son nom, sa couleur, ses groupes de champs, ses champs, et ses options) et importé dans n'importe quel compte KolleK. Consultez @doc(collectionTypes.importExport).

C'est la liste complète et honnête.

## Ce que vous ne pouvez pas encore exporter

Il n'existe actuellement aucune fonction intégrée d'export des objets, des exemplaires, des photos, ou de collections entières, ni d'import correspondant. Les données de votre catalogue ne peuvent pas encore être extraites de l'application sous forme de fichier depuis l'interface.

:::note
L'import et l'export des objets et des collections figurent parmi les fonctionnalités prévues. La @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités") est le registre tenu à jour de la situation actuelle, alors consultez la plutôt que de faire des suppositions.
:::

Si vous avez besoin d'un accès structuré à vos données aujourd'hui, l'@doc(api.overview, "API JSON") peut lire tout ce qui se trouve dans votre compte, ce qui constitue une solution viable pour les personnes à l'aise avec la technique.

## Le véritable chemin de sauvegarde aujourd'hui

Si votre instance est auto hébergée, la sauvegarde fiable se fait au niveau de l'instance : un dump de la base de données plus une archive du volume de stockage qui contient les photos et les documents. Cela capture absolument tout, y compris ce que l'export intégré à l'application ne peut pas atteindre. Le guide complet se trouve dans @doc(selfHosting.backupAndRestore).

Si quelqu'un d'autre héberge KolleK pour vous, c'est cette personne qui détient cette capacité de sauvegarde. Demandez-lui quelles sont ses dispositions de sauvegarde ; c'est une question juste et importante.

## Pour aller plus loin

- Vous êtes en auto hébergement ? Mettez en place de vraies sauvegardes dans @doc(selfHosting.backupAndRestore).
- Déplacer une configuration de type entre comptes est couvert dans @doc(collectionTypes.importExport).
- Découvrez ce qui est prévu par ailleurs sur la @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités").
