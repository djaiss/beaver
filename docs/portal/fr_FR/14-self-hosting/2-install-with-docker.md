---
id: selfHosting.installDocker
title: Installer avec Docker
slug: installer-avec-docker
section: auto-hebergement
---

# Installer avec Docker

Ceci est le guide d'installation faisant autorité. Il vous mène d'une machine équipée de Docker à une instance KolleK en fonctionnement, avec votre premier compte créé. Comptez environ quinze minutes pour l'ensemble.

Le fichier `docker/README.md` du dépôt documente la même procédure du point de vue de l'opérateur et reste synchronisé avec le code. Si cette page et ce fichier venaient à diverger, faites confiance à `docker/README.md`.

## Avant de commencer

Vous avez besoin de :

- Une machine avec **Docker Engine 24 ou plus récent** et le **plugin Compose** (`docker compose`).
- Une copie du dépôt KolleK, clonée ou téléchargée.
- Dix minutes d'attention pour le fichier d'environnement. C'est là que se produisent les erreurs qui comptent.

Rien d'autre. La stack apporte sa propre base de données MySQL, et les sessions, le cache et la file d'attente reposent sur la base de données, il n'y a donc pas de Redis à installer.

## Installation

::::steps
:::step title="Créez votre fichier d'environnement"
Depuis la racine du dépôt, copiez le modèle d'environnement Docker :

```bash
cp .env.docker.example .env
```

Ce fichier pilote l'ensemble de la stack. Vous allez le modifier dans les deux étapes suivantes.
:::

:::step title="Générez la clé d'application"
Générez une clé et copiez le résultat affiché :

```bash
docker compose run --rm app php artisan key:generate --show
```

Collez la valeur affichée dans `.env` sous `APP_KEY`. Cette clé chiffre vos données au repos. **Définissez-la maintenant et ne la changez jamais par la suite.** Une clé modifiée rend chaque champ chiffré et chaque session définitivement illisibles. Lisez @doc(selfHosting.applicationKeyAndEncryption) avant d'aller plus loin si ce n'est pas déjà fait.
:::

:::step title="Vérifiez les mots de passe et l'URL"
Dans `.env`, changez `DB_PASSWORD` et `DB_ROOT_PASSWORD` par rapport à leurs valeurs de remplacement, et définissez `APP_URL` avec l'adresse que vos utilisateurs visiteront. La valeur par défaut est `http://localhost:8000`, ce qui convient pour un premier essai sur votre propre machine.
:::

:::step title="Démarrez la stack"
Construisez et démarrez tout :

```bash
docker compose up -d --build
```

La première construction prend quelques minutes. Une fois terminée, le conteneur web applique automatiquement les migrations de base de données et l'instance démarre à l'adresse `APP_URL`.
:::

:::step title="Créez votre premier compte"
Ouvrez l'URL dans un navigateur et utilisez la page d'inscription pour vous inscrire. Cela crée votre utilisateur personnel et votre premier compte, exactement comme décrit dans @doc(accounts.create).

::screenshot{label="Page d'inscription d'une instance fraîchement installée"}
:::

:::step title="Accordez-vous l'accès administrateur de l'instance"
Si vous souhaitez accéder au panneau d'administration à l'échelle du serveur, accordez le drapeau à votre utilisateur :

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Voyez @doc(instanceAdmin.grantAccess) pour savoir ce que cela vous donne, et ce que cela ne vous donne pas.
:::
::::

## Ce qui tourne réellement

La stack Compose démarre quatre conteneurs. Trois d'entre eux exécutent la même image KolleK dans des rôles différents, choisis par la variable d'environnement `CONTAINER_ROLE` :

- **app** sert l'application web à travers nginx et PHP. C'est le seul conteneur qui exécute les migrations de base de données, et il le fait au démarrage.
- **queue** traite les tâches en arrière-plan (e-mails, livraisons, journalisation) des files `high`, `default` et `low`.
- **scheduler** déclenche les tâches de maintenance quotidiennes décrites dans @doc(selfHosting.scheduledJobs).

Le quatrième conteneur est **mysql**, exécutant MySQL 8.4.

Vos données vivent dans deux volumes Docker nommés, indépendants des conteneurs : `db-data` pour la base de données et `storage-data` pour les photos et documents téléversés. Les conteneurs peuvent être reconstruits et remplacés librement ; les volumes persistent.

:::note
Les trois conteneurs applicatifs doivent partager le même `.env`, et surtout la même `APP_KEY`. Le fichier Compose organise déjà cela. Conservez cette propriété si vous personnalisez la configuration.
:::

## Si vous préférez exécuter les migrations vous-même

Par défaut, le conteneur web migre la base de données à chaque démarrage, ce qui rend les mises à jour totalement automatiques. Si vous voulez un contrôle manuel, définissez `RUN_MIGRATIONS=false` dans `.env`, puis exécutez les migrations vous-même quand nécessaire :

```bash
docker compose exec app php artisan migrate --force
```

## Et ensuite

- Parcourez @doc(selfHosting.configure) pour comprendre ce que `.env` contrôle d'autre.
- Faites fonctionner l'e-mail dans @doc(selfHosting.setupEmailDelivery). Tant que ce n'est pas fait, les invitations et les liens de connexion partent vers un fichier de journal plutôt qu'une boîte de réception.
- Mettez en place les @doc(selfHosting.backupAndRestore, "sauvegardes") avant d'y placer de vraies données.
