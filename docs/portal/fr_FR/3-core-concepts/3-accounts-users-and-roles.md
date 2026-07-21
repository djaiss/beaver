---
id: accounts.usersAndRoles
title: Comptes, utilisateurs et rôles
slug: accounts-users-and-roles
section: core-concepts
---

# Comptes, utilisateurs et rôles

KolleK est construit autour d'un espace de travail unique, le compte, et des personnes qui le partagent. Cette page explique la frontière et le modèle de permissions en termes simples, afin que rien concernant l'accès ne vous surprenne jamais.

## Le compte est la frontière

Un **compte** est un espace de travail privé. Chaque collection, objet, exemplaire, type, étiquette et emplacement vit à l'intérieur d'exactement un compte. Rien ne fuit d'un compte à l'autre, et personne en dehors du vôtre ne peut voir son contenu, à moins que vous ne choisissiez délibérément de @doc(sharing.overview, "partager une collection").

Quand Emma s'est inscrite, KolleK a créé deux choses en même temps : son utilisateur personnel, et un nouveau compte qu'elle possède. Si elle invite son partenaire Sam, il rejoint son compte et travaille dans le même catalogue.

## Une personne, un compte

Un **utilisateur** est une personne authentifiée, liée à une adresse e-mail, et un utilisateur appartient à exactement un compte.

:::note
La même adresse e-mail ne peut pas se trouver dans deux comptes. Quelqu'un qui possède déjà son propre compte ne peut pas accepter une invitation à rejoindre le vôtre. S'il souhaite vous rejoindre, il devra utiliser une adresse e-mail différente, ou supprimer d'abord son propre compte.
:::

## Les trois rôles

Chaque membre d'un compte a un rôle, choisi au moment de son invitation et modifiable ensuite par un propriétaire.

- Un **lecteur** (viewer) peut parcourir tout le contenu du compte, mais ne peut rien créer ni modifier. Leo, l'ami d'Emma, est lecteur : il peut admirer le catalogue, pas le modifier.
- Un **éditeur** peut créer et modifier le contenu du catalogue : collections, objets, exemplaires, photos, et tous les enregistrements d'historique. Sam est éditeur.
- Un **propriétaire** peut faire tout ce qu'un éditeur peut faire, et gère aussi le compte lui-même : inviter et retirer des membres, changer les rôles, gérer les paramètres du compte, et supprimer le compte. Emma est propriétaire.

La lecture est ouverte à tout membre, y compris les lecteurs. L'écriture nécessite le rôle d'éditeur ou de propriétaire. L'administration du compte nécessite le rôle de propriétaire. La page @doc(collaboration.rolesInPractice, "les rôles en pratique") met cela en correspondance avec des tâches concrètes si vous souhaitez le tableau complet.

Un compte doit toujours garder au moins un propriétaire. KolleK ne laissera jamais le dernier propriétaire être rétrogradé ou retiré, afin qu'un compte ne puisse jamais se retrouver bloqué.

## Un indicateur qui n'est pas un rôle

Si vous entendez parler d'un **administrateur d'instance**, il s'agit d'une chose totalement différente. C'est un indicateur à l'échelle du serveur, destiné à quiconque exploite l'installation de KolleK elle-même. Il n'accorde rien de plus à l'intérieur du propre compte de cette personne, et n'a rien à voir avec les rôles lecteur, éditeur ou propriétaire. Il est couvert dans @doc(instanceAdmin.panel, "le panneau d'administration de l'instance") pour les opérateurs.

## Et ensuite

- Faites entrer quelqu'un avec @doc(collaboration.invitePeople).
- Changez ce qu'un membre peut faire dans @doc(collaboration.manageMembersAndRoles).
- Poursuivez les concepts avec @doc(collections.overview).
