---
id: selfHosting.configure
title: Configurer votre instance
slug: configure-your-instance
section: self-hosting
---

# Configurer votre instance

Tout ce qui concerne votre instance se configure via le fichier `.env` que vous avez créé lors de l'@doc(selfHosting.installDocker, "installation"). Cette page passe en revue les paramètres qu'un opérateur manipule réellement, regroupés selon leur fonction, plutôt que de lister chaque variable présente dans le modèle.

Après avoir modifié `.env`, appliquez le changement en recréant les conteneurs :

```bash
docker compose up -d
```

## Identité et URL

- `APP_NAME` est le nom affiché dans l'interface et dans les e-mails. Sa valeur par défaut est `Kollek`.
- `APP_URL` est l'adresse publique de votre instance. Les liens dans les e-mails sont construits à partir de cette valeur, elle doit donc correspondre à l'adresse réellement utilisée par vos utilisateurs.
- `APP_PORT` est le port de l'hôte publié par le conteneur web, `8000` par défaut.

## La clé d'application

`APP_KEY` chiffre les données sensibles au repos. Vous la définissez une fois lors de l'installation et ne la changez jamais à la légère. Elle est suffisamment importante pour avoir @doc(selfHosting.applicationKeyAndEncryption, "sa propre page"), qui traite aussi du mécanisme de rotation `APP_PREVIOUS_KEYS`.

## Base de données

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` et `DB_ROOT_PASSWORD` configurent le conteneur MySQL fourni. Changez les deux mots de passe par rapport à leurs valeurs de remplacement avant le premier démarrage. `RUN_MIGRATIONS` contrôle si le conteneur web migre au démarrage (`true` par défaut).

## E-mail

`MAIL_MAILER` détermine comment les e-mails quittent votre instance, et sa valeur par défaut est `log`.

:::note
Avec le mailer `log` par défaut, aucun e-mail n'est jamais envoyé. Les invitations, liens magiques, réinitialisations de mot de passe et alertes de sécurité sont écrits dans le journal de l'application à la place. Configurer un véritable service d'envoi est le seul réglage dont presque toutes les instances ont besoin. Voyez @doc(selfHosting.setupEmailDelivery).
:::

## Stockage des fichiers

`FILESYSTEM_DISK` vaut `local` par défaut : les photos et documents téléversés sont stockés dans le volume `storage-data`. Pour utiliser à la place un stockage objet compatible S3, définissez cette valeur à `s3` et renseignez les variables `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` et, pour les fournisseurs autres qu'AWS, `AWS_ENDPOINT`. Les fichiers sont servis aux utilisateurs via des routes privées vérifiées par compte, quel que soit le cas, jamais comme des URL publiques.

## Entretien courant

- `TRASH_RETENTION_DAYS` définit combien de temps les objets supprimés en douceur restent dans la @doc(dataSafety.restoreFromTrash, "corbeille") avant que la purge nocturne ne les supprime définitivement. La valeur par défaut est de 30 jours.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` est l'adresse notifiée lorsqu'un utilisateur supprime son propre utilisateur ou est retiré par le @doc(users.inactiveDeletion, "nettoyage d'inactivité"). Faites-la pointer vers vous-même afin qu'aucun départ ne passe inaperçu.

## Le site vitrine public

`SHOW_MARKETING_SITE` vaut `false` par défaut, ce qui signifie que votre instance ne sert que l'application elle-même. Définissez cette valeur à `true` pour servir aussi les pages vitrine publiques et la référence API générée à `/docs/api`. La plupart des instances privées laissent ce réglage désactivé ; activez-le si vos développeurs souhaitent que la référence API soit servie localement.

## Ce que vous n'avez pas besoin de configurer

Les sessions (`SESSION_DRIVER`), le cache (`CACHE_STORE`) et la file d'attente (`QUEUE_CONNECTION`) reposent tous sur `database` par défaut. Les valeurs par défaut sont correctes pour la stack fournie, et il n'y a pas de Redis ni d'autre service à ajouter. Laissez-les inchangées à moins de savoir précisément pourquoi vous les modifiez.

## Et ensuite

- Faites circuler de vrais e-mails dans @doc(selfHosting.setupEmailDelivery).
- Comprenez la clé que vous devez protéger dans @doc(selfHosting.applicationKeyAndEncryption).
- Mettez en place les @doc(selfHosting.backupAndRestore, "sauvegardes").
