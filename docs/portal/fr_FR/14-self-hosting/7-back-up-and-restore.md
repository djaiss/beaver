---
id: selfHosting.backupAndRestore
title: Sauvegarder et restaurer votre instance
slug: sauvegarder-et-restaurer
section: auto-hebergement
---

# Sauvegarder et restaurer votre instance

Il n'existe pas de sauvegarde automatisée à l'intérieur de KolleK. Protéger les données est le travail de l'opérateur, et cette page en est la procédure. C'est aussi, aujourd'hui, la vraie réponse à « comment tout exporter », comme l'explique @doc(dataSafety.backupCollectionData) du point de vue du collectionneur.

## Ce qu'est une sauvegarde complète

Trois éléments, et les trois comptent :

1. **La base de données**, dans le volume `db-data`. Chaque enregistrement : comptes, collections, objets, exemplaires, historique.
2. **Le volume de stockage**, `storage-data`. Chaque photo et document téléversé.
3. **La clé d'application**, `APP_KEY` de votre `.env` (plus `APP_PREVIOUS_KEYS` si définie).

:::warning
Une sauvegarde sans sa clé d'application correspondante n'est pas une sauvegarde. Les champs chiffrés se restaurent en texte chiffré illisible sans la clé qui les a écrits. Conservez la clé avec, ou à côté de, chaque sauvegarde que vous effectuez. Voyez @doc(selfHosting.applicationKeyAndEncryption).
:::

## Sauvegarder

Exportez la base de données :

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

Archivez le volume de stockage :

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

Copiez les deux fichiers, ainsi qu'une copie de votre `.env`, quelque part en dehors du serveur. Automatisez cela avec une tâche cron nocturne et conservez plus d'une génération ; une sauvegarde que vous n'avez jamais restaurée est un espoir, pas un plan.

## Restaurer

Sur une machine neuve, restaurez dans cet ordre :

1. Installez la même version de KolleK en suivant @doc(selfHosting.installDocker), mais définissez `APP_KEY` (et `APP_PREVIOUS_KEYS`) à partir de votre sauvegarde plutôt que de générer une nouvelle clé.
2. Démarrez la stack une fois afin que les volumes existent, puis chargez l'export de base de données :

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. Décompressez l'archive de stockage dans le volume de stockage :

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. Redémarrez la stack avec `docker compose up -d` et connectez-vous pour vérifier.

## La commande qui supprime tout

:::warning
`docker compose down -v` supprime les volumes nommés, c'est-à-dire la base de données et chaque fichier téléversé. N'utilisez jamais l'option `-v` sur une instance réelle. Un simple `docker compose down` est sûr et laisse les volumes intacts.
:::

## Et ensuite

- Comprenez ce que la clé protège dans @doc(selfHosting.applicationKeyAndEncryption).
- Découvrez ce que les collectionneurs peuvent exporter depuis l'application dans @doc(dataSafety.backupCollectionData).
