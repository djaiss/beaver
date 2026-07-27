---
id: instanceAdmin.panel
title: Le panneau d'administration de l'instance
slug: panneau-dadministration
section: auto-hebergement
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

## Options du site

La rubrique **Options du site** regroupe les réglages du site vitrine public, les pages qu'un visiteur voit avant de se connecter. Ce site est désactivé par défaut sur une instance auto hébergée (voir @doc(selfHosting.configure)) : si tu ne l'as jamais activé, rien ici ne change ce que voient tes utilisateurs.

### La bannière d'annonce

La bannière est la barre noire en haut de chaque page du site vitrine. Elle est faite pour une phrase courte : une version que tu veux faire remarquer, une fenêtre de maintenance, un événement.

Seule la phrase est nécessaire. Tout le reste est facultatif :

- **Afficher la bannière** l'active ou la désactive. Mets-la sur **Non** et aucune barre n'apparaît, quoi que tu aies rempli par ailleurs.
- **Version** est la petite pastille verte à gauche, par exemple `v0.9`. Laisse le champ vide et la pastille disparaît.
- **Lien** est l'adresse vers laquelle pointe la bannière, et **Libellé du lien** le texte sur lequel le visiteur clique. Laisse le lien vide pour une bannière qui se contente d'annoncer quelque chose.
- **Phrase** est l'annonce elle-même.

Le site vitrine est servi en plusieurs langues, donc la phrase et le libellé du lien s'écrivent une langue à la fois, avec un onglet pour chacune. Une langue laissée vide retombe sur l'anglais, ce qui veut dire que remplir l'anglais seul donne déjà une bannière à tous les visiteurs. Le point vert sur un onglet indique que cette langue a sa propre phrase.

L'aperçu au-dessus du formulaire montre la barre telle que le visiteur la verra, dans la langue de l'onglet où tu te trouves. L'enregistrement vide pour toi le cache des pages du site vitrine, donc le changement est visible tout de suite.

### Vider le cache des réponses

Les pages du site vitrine changent rarement, donc chacune est rendue une fois puis servie depuis un cache pendant sept jours. Le site public reste rapide, mais une modification peut aussi rester invisible pendant une semaine.

**Vider le cache** supprime d'un coup toutes les pages en cache. Utilise-le après avoir changé quelque chose que le site public affiche et dont l'application n'a pas connaissance, par exemple une page de documentation modifiée sur le serveur. Enregistrer la bannière et modérer un témoignage vident déjà le cache d'eux-mêmes.

Vider ne perd rien. Chaque page est rendue à nouveau à la prochaine demande, et le seul coût est que le premier visiteur attend ce rendu. La même chose se fait en ligne de commande avec `php artisan responsecache:clear`, décrite dans @doc(selfHosting.cliCommands).

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
