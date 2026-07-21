---
id: selfHosting.cliCommands
title: Administrer avec la ligne de commande
slug: cli-commands
section: self-hosting
---

# Administrer avec la ligne de commande

Quelques tâches d'opérateur vivent sur la ligne de commande plutôt que dans l'application web. Cette page liste les commandes artisan dont vous pourriez réellement avoir besoin en exploitant une instance, avec un renvoi vers la page plus complète pour chacune.

Sur une installation Docker, exécutez chaque commande à travers le conteneur web :

```
docker compose exec app php artisan <command>
```

## Fonctionnement au quotidien

### Accorder ou révoquer l'administration de l'instance

```
php artisan beaver:make-instance-administrator you@example.com
php artisan beaver:make-instance-administrator you@example.com --revoke
```

Accorde (ou retire) le drapeau d'administrateur à l'échelle du serveur pour l'utilisateur ayant cette adresse e-mail. C'est ainsi que le premier administrateur est amorcé après l'installation. Voyez @doc(instanceAdmin.grantAccess).

### Créer un point de terminaison webhook

```
php artisan beaver:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Enregistre un point de terminaison webhook pour un utilisateur et affiche son identifiant et son secret de signature. Les utilisateurs peuvent aussi faire cela eux-mêmes depuis les paramètres de leur profil. Notez qu'aucun événement applicatif ne déclenche encore de webhooks ; voyez @doc(webhooks.overview).

### Reconstruire l'index de recherche des photos

```
php artisan photos:rebuild-search-index
```

Reconstruit l'index de recherche derrière la bibliothèque de photos et comble les dimensions d'image manquantes. Exécutez-la une fois après une mise à jour vers une version qui introduit l'écran des photos. Elle peut être exécutée à nouveau à tout moment sans risque ; elle ignore les photos dont les fichiers sont manquants et ne change rien d'autre. Voyez @doc(selfHosting.upgrade).

### Amorcer une locale pour la traduction

```
php artisan beaver:localize fr_FR
```

Extrait chaque chaîne traduisible de l'application et la synchronise dans le fichier JSON de la locale. Voyez @doc(selfHosting.addLanguage).

## Réservé au développement

Deux autres commandes existent dans le code source, et aucune n'a sa place sur une instance de production. `beaver:bruno` réinitialise la base de données avec des données de départ pour les tests de client API, ce qui détruirait de vraies données, et `beaver:sync-skills` maintient l'outillage propre au projet. Vous pouvez ignorer les deux en tant qu'opérateur.

:::warning
N'exécutez jamais `beaver:bruno` sur une instance réelle. Elle efface la base de données et la ré-ensemence avec des données de démonstration.
:::

## Et ensuite

- Amorcez votre administrateur dans @doc(instanceAdmin.grantAccess).
- Gardez l'instance à jour avec @doc(selfHosting.upgrade).
- Traduisez l'interface dans @doc(selfHosting.addLanguage).
