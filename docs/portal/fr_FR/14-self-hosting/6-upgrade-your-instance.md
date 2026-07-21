---
id: selfHosting.upgrade
title: Mettre à jour votre instance
slug: upgrade-your-instance
section: self-hosting
---

# Mettre à jour votre instance

La mise à jour de KolleK est conçue pour être sans surprise : récupérer la nouvelle version, reconstruire, terminé. Cette page explique pourquoi c'est sûr, et la seule étape à connaître après une mise à jour.

## Pourquoi les mises à jour ne perdent pas de données

Deux propriétés rendent le chemin de mise à jour sûr :

- **Vos données vivent dans des volumes nommés** (`db-data` pour la base de données, `storage-data` pour les fichiers), indépendants des conteneurs et de l'image. Reconstruire les conteneurs ne les touche pas.
- **Les migrations n'avancent que dans un sens.** Le conteneur web applique les migrations de base de données en attente au démarrage avec `migrate --force`, et KolleK ne livre jamais de migration qui réinitialise ou réécrit destructivement des données. Une mise à jour ne fait qu'ajouter à votre schéma.

## Mise à jour

::::steps
:::step title="Sauvegardez d'abord"
Effectuez un export de base de données et une archive du stockage comme décrit dans @doc(selfHosting.backupAndRestore). Les mises à jour sont sûres par conception, mais une sauvegarde transforme « sûr par conception » en « sûr, tout court ».
:::

:::step title="Récupérez la nouvelle version"
Depuis le répertoire du dépôt, récupérez la version vers laquelle vous mettez à jour :

```bash
git pull
```
:::

:::step title="Reconstruisez et redémarrez"
```bash
docker compose up -d --build
```

Compose reconstruit l'image et recrée les conteneurs. Au démarrage, le conteneur web applique automatiquement toute nouvelle migration, puis l'instance est de nouveau disponible à l'adresse `APP_URL`.
:::
::::

Si vous préférez garder les migrations sous contrôle manuel, définissez `RUN_MIGRATIONS=false` et exécutez vous-même `docker compose exec app php artisan migrate --force` dans le cadre de la procédure, comme indiqué dans @doc(selfHosting.installDocker).

## L'étape de l'index de recherche des photos

Une mise à jour comprend une tâche de maintenance ponctuelle : les instances antérieures à l'écran de la bibliothèque de photos doivent voir leur index de recherche de photos construit une fois, sans quoi la recherche de photos reste vide pour les photos existantes.

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

La commande est idempotente et peut être exécutée sans risque sur n'importe quelle instance, donc en cas de doute, exécutez-la. Elle comble également les dimensions d'image manquantes pour les photos téléversées avant que ces dimensions ne soient enregistrées.

:::note
Ne changez pas `APP_KEY` dans le cadre d'une mise à jour. La clé survit à chaque version. Si un guide de mise à jour semble un jour demander une nouvelle clé, vous le lisez mal. Voyez @doc(selfHosting.applicationKeyAndEncryption).
:::

## Et ensuite

- Gardez vos @doc(selfHosting.backupAndRestore, "sauvegardes") à jour afin que chaque mise à jour parte de l'une d'entre elles.
- Passez en revue @doc(selfHosting.scheduledJobs), qui reprennent automatiquement dès que le conteneur scheduler est de nouveau en fonctionnement.
