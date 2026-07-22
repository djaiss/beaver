---
id: instanceAdmin.grantAccess
title: Accorder l'accès administrateur de l'instance
slug: acces-administrateur
section: auto-hebergement
---

# Accorder l'accès administrateur de l'instance

Un administrateur de l'instance est la personne qui s'occupe du serveur lui-même, avec un panneau qui voit à travers tous les comptes de l'instance. Cette page explique ce qu'est ce drapeau, comment l'accorder, et les garde-fous qui l'entourent.

## Ce qu'est ce drapeau, et ce qu'il n'est pas

Le drapeau d'administrateur de l'instance est à l'échelle du serveur et totalement séparé des @doc(accounts.usersAndRoles, "rôles de compte"). Il accorde exactement une chose : l'accès au @doc(instanceAdmin.panel, "panneau d'administration de l'instance").

- Il n'accorde aucun pouvoir supplémentaire à l'intérieur du compte de l'administrateur lui-même. Un administrateur de l'instance qui est simple lecteur dans son compte ne peut toujours pas y modifier les objets.
- Il s'applique par utilisateur, pas par compte. Accordez-le à la personne précise qui exploite le serveur, généralement vous-même.

Alex, qui gère l'instance du club, détient le drapeau sur son propre utilisateur et est un propriétaire ordinaire à l'intérieur de son propre compte. Les deux faits sont indépendants.

## Accorder et révoquer

Le drapeau est géré depuis la ligne de commande, ce qui est délibéré : l'accès initial au panneau à l'échelle du serveur doit nécessiter un accès au serveur.

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Révoquez-le de la même façon :

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com --revoke
```

Un administrateur existant peut aussi basculer le drapeau sur d'autres utilisateurs depuis le panneau.

## Pourquoi le panneau fait comme s'il n'existait pas

Pour quiconque n'a pas le drapeau, `/instance-admin` répond **404 Not Found**, pas « accès refusé ». Le panneau n'annonce pas son existence aux personnes qui ne peuvent pas l'utiliser, si bien que sonder une instance ne révèle rien. Si vous vous êtes accordé le drapeau et voyez toujours un 404, vérifiez que vous êtes connecté avec l'utilisateur exact auquel vous l'avez accordé.

## Les garde-fous contre le verrouillage

Deux règles protègent l'instance contre la perte de son administrateur :

- Un administrateur ne peut pas révoquer son propre drapeau depuis le panneau.
- Un administrateur ne peut pas supprimer son propre utilisateur depuis le panneau.

Ainsi, le panneau ne peut jamais servir à enfermer tout le monde hors du panneau. Et même si tous les administrateurs venaient à disparaître, le chemin en ligne de commande ci-dessus fonctionne toujours, car il ne nécessite qu'un accès au serveur.

## Et ensuite

- Découvrez ce que le panneau peut faire dans @doc(instanceAdmin.panel).
- Parcourez les autres commandes de l'opérateur dans @doc(selfHosting.cliCommands).
