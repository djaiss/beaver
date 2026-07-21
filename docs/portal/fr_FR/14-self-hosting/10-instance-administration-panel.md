---
id: instanceAdmin.panel
title: Le panneau d'administration de l'instance
slug: instance-administration-panel
section: self-hosting
---

# Le panneau d'administration de l'instance

Le panneau d'administration de l'instance, à `/instance-admin`, est l'endroit où un @doc(instanceAdmin.grantAccess, "administrateur de l'instance") voit à travers tous les comptes du serveur : combien il y en a, qui en fait partie, et la poignée d'actions destructrices que seul un opérateur devrait détenir. Cette page décrit ce que le panneau peut faire, et tout aussi important, ce qu'il ne peut délibérément pas faire.

Si vous gérez une instance personnelle avec un seul compte, vous n'aurez peut-être jamais besoin de ce panneau. Il fait ses preuves sur les instances partagées, comme un serveur de club ou de famille avec plusieurs comptes.

:::note
Le panneau n'apparaît que pour les utilisateurs portant le drapeau d'administrateur de l'instance. Toute autre personne visitant `/instance-admin` obtient une page introuvable, pas une page d'accès refusé, si bien que le panneau n'annonce jamais son existence.
:::

## L'aperçu

Le panneau s'ouvre sur un aperçu de l'instance entière :

- Le nombre de **comptes**, d'**utilisateurs**, de **collections** et d'**objets** sur l'ensemble du serveur.
- Les **comptes créés ce mois-ci** et les **utilisateurs actifs ce mois-ci**, pour voir si l'instance grandit ou est calme.
- Un graphique des **inscriptions par mois** sur les douze derniers mois.

Ces chiffres sont à l'échelle de l'instance. Ils ne révèlent le contenu du catalogue de personne.

## Parcourir les comptes

La section **Comptes** liste tous les comptes de l'instance, 25 par page, avec le nombre de membres et de collections de chaque compte.

Vous pouvez rechercher des comptes **par l'adresse e-mail d'un membre** et filtrer par rôle. Rechercher par nom de compte ou de personne n'est pas possible, car les noms sont chiffrés dans la base de données et ne peuvent pas y être recherchés. L'e-mail est le point d'ancrage fiable.

Ouvrir un compte affiche ses membres, triés propriétaires d'abord, puis éditeurs, puis lecteurs, ainsi que le nombre de collections et d'objets du compte et ses quinze entrées de journal d'activité les plus récentes.

## Les actions destructrices

Trois actions du panneau modifient ou suppriment des données, et aucune d'entre elles n'est réversible :

- **Supprimer un compte**, qui retire le compte avec chaque collection, objet, exemplaire, membre et tout l'historique qu'il contient.
- **Supprimer un utilisateur**, qui retire cette personne de son compte.
- **Basculer le drapeau administrateur d'un autre utilisateur**, qui accorde ou révoque l'administration de l'instance pour quelqu'un d'autre.

:::warning
Supprimer un compte ou un utilisateur depuis ce panneau est immédiat et permanent. Rien ne passe par la corbeille, et il n'existe aucune restauration. Vérifiez deux fois que vous avez le bon compte ou la bonne personne avant de confirmer.
:::

Deux garde-fous protègent l'instance elle-même : un administrateur ne peut pas révoquer son propre drapeau, et ne peut pas supprimer son propre utilisateur depuis le panneau. Quelle que soit la façon dont il est utilisé, l'instance conserve toujours au moins un administrateur opérationnel.

## Ce que le panneau n'est pas

Le panneau est conçu pour être uniquement accessible sur le web. L'API JSON est cantonnée à un seul compte, et une surface à l'échelle de l'instance n'a pas sa place dans celle-ci, si bien qu'aucune de ces capacités n'existe sous forme de points de terminaison d'API.

Les sections **Support** et **Avis** visibles dans le panneau sont des emplacements réservés et ne sont pas encore construites. Voyez @doc(troubleshooting.featureStatus).

## Et ensuite

- Accordez ou révoquez le drapeau lui-même dans @doc(instanceAdmin.grantAccess).
- Comprenez ce que les propriétaires de compte peuvent déjà faire sans vous dans @doc(collaboration.manageMembersAndRoles).
- Passez en revue les autres outils de l'opérateur dans @doc(selfHosting.cliCommands).
