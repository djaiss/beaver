---
id: accounts.delete
title: Supprimer un compte
slug: delete-an-account
section: data-safety
---

# Supprimer un compte

Supprimer un compte est l'action la plus destructrice de KolleK. Elle supprime tout l'espace de travail : chaque collection, chaque objet, chaque exemplaire avec tout son historique, chaque photo et document, et l'accès de chaque membre. Seul un @doc(accounts.usersAndRoles, "propriétaire") peut le faire.

:::warning
Supprimer un compte ne peut pas être annulé. Rien ne va à la corbeille, rien ne peut être restauré, et personne, y compris la personne qui gère l'instance, ne peut le récupérer. Chaque membre perd tout instantanément.
:::

## Avant de supprimer

Prenez le temps de vérifier trois choses.

- **Est-ce vraiment ce que vous voulez, plutôt que @doc(users.deleteSelf, "supprimer votre propre utilisateur") ?** Quitter un compte partagé nécessite seulement de vous retirer vous-même. Le compte et le catalogue survivent sans vous.
- **Quelqu'un d'autre en dépend-il ?** Chaque membre du compte perd l'accès et les données au moment où vous confirmez. Prévenez-les d'abord.
- **Avez vous récupéré ce dont vous avez besoin ?** Exportez les @doc(collectionTypes.importExport, "définitions de types de collection") que vous voulez conserver. Si l'instance est auto hébergée, effectuez d'abord une sauvegarde complète, comme décrit dans @doc(selfHosting.backupAndRestore). Après la suppression, il ne reste rien à sauvegarder.

## Supprimer le compte

Depuis **Paramètres du compte**, trouvez l'option de suppression dans la zone de danger, et confirmez. Le compte et tout son contenu sont supprimés, et tous les membres sont déconnectés définitivement.

## Ce qui disparaît ensuite

Tout. Les collections, les objets, les exemplaires, les catégories, les séries, les collections de séries, les étiquettes, les emplacements, les types et champs personnalisés, les photos, les documents, l'historique complet des exemplaires, le journal d'activité, tous les membres, et toutes les invitations en attente. Les adresses e-mail concernées redeviennent disponibles pour créer de nouveaux comptes, mais ces comptes partent vides.

## Pour aller plus loin

- Vous retirer seulement vous-même est couvert dans @doc(users.deleteSelf).
- Pour les suppressions récupérables, consultez @doc(dataSafety.restoreFromTrash).
