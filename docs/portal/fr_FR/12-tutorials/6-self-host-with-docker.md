---
id: tutorials.selfHostWithDocker
title: "Tutoriel : auto-héberger KolleK avec Docker"
slug: self-host-with-docker
section: tutorials
---

# Tutoriel : auto-héberger KolleK avec Docker

Dans ce tutoriel, vous ferez passer une machine sans rien dessus à une instance KolleK fonctionnelle : cloner le projet, configurer l'environnement, générer la clé d'application, démarrer la pile, créer le premier compte, et accorder le premier administrateur d'instance. À la fin, vous aurez une instance opérationnelle et saurez où prennent le relais les guides opérationnels plus approfondis.

Nous allons suivre Alex, qui met en place une instance pour son club de collectionneurs sur un petit serveur domestique. Les étapes sont identiques sur un VPS ou un ordinateur portable.

Comptez quinze à trente minutes pour ce tutoriel, la majeure partie étant passée à attendre la première construction.

## Avant de commencer

Vous avez besoin de :

- Une machine avec **Docker Engine 24 ou plus récent** et le **plugin Compose** (la commande `docker compose`, pas l'ancienne `docker-compose`).
- **Git**, pour cloner le projet.
- Un terminal et une aisance de base pour y exécuter des commandes.

Il est également utile de parcourir d'abord la @doc(selfHosting.index, "vue d'ensemble de l'auto-hébergement"), car elle introduit la règle unique sur laquelle ce tutoriel va insister : la clé d'application est définie une fois et jamais changée.

## Étape 1 : cloner le projet et créer votre configuration

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

Le fichier `.env` est la configuration de votre instance. Tout ce qu'un opérateur touche habituellement s'y trouve, et le @doc(selfHosting.configure, "guide de configuration") le parcourt groupe par groupe. Pour un premier démarrage, seules les deux prochaines étapes sont obligatoires.

## Étape 2 : générer la clé d'application

KolleK chiffre les données sensibles au repos avec une clé que vous générez une fois.

```bash
docker compose run --rm app php artisan key:generate --show
```

Copiez le résultat (il commence par `base64:`) et collez-le dans `.env` comme valeur de `APP_KEY`.

:::warning
Définissez la clé d'application une fois et ne la changez jamais sur une instance en cours d'exécution. Tout ce qui est chiffré, ce qui inclut les noms, les objets, et les sessions, devient définitivement illisible sous une clé différente. Conservez une copie de la clé en lieu sûr, car une sauvegarde de base de données n'est restaurable qu'avec la clé qui l'a chiffrée.
:::

L'histoire complète, y compris la façon dont une rotation de clé délibérée est prise en charge, se trouve dans @doc(selfHosting.applicationKeyAndEncryption).

## Étape 3 : vérifier les mots de passe et l'URL

Ouvrez `.env` dans un éditeur et vérifiez trois choses.

- **`DB_PASSWORD` et `DB_ROOT_PASSWORD`.** Les deux sont livrés avec des valeurs de substitution. Remplacez-les par des mots de passe forts qui vous appartiennent avant le premier démarrage, car c'est au premier démarrage que la base de données est créée avec eux.
- **`APP_URL`.** L'adresse que vos utilisateurs saisiront. Alex règle `http://server.local:8000` pour le réseau du club. La valeur par défaut est `http://localhost:8000`.
- **`APP_PORT`.** Le port publié, `8000` sauf si vous le changez.

## Étape 4 : démarrer la pile

```bash
docker compose up -d --build
```

La première exécution construit l'image et prend quelques minutes. Compose démarre ensuite quatre conteneurs.

- **app**, le serveur web. C'est le seul rôle qui exécute les migrations de base de données, de sorte que le schéma n'est mis en place qu'une seule fois.
- **queue**, le worker en arrière-plan qui envoie les e-mails et traite les tâches.
- **scheduler**, qui exécute les tâches de maintenance quotidiennes.
- **mysql**, la base de données.

Vérifiez que tout est démarré avec `docker compose ps`. Quand le conteneur app se signale en bonne santé, ouvrez votre `APP_URL` dans un navigateur. Vous devriez voir l'écran de connexion de KolleK.

## Étape 5 : créer le premier compte

Rendez-vous sur la page d'inscription et créez un compte. Cela fonctionne exactement comme pour n'importe quel utilisateur, le guide se trouve dans @doc(accounts.create), et cela fait de vous le propriétaire du premier compte de l'instance.

Alex s'inscrit, atterrit sur la liste de contrôle de prise en main, et résiste à l'envie de cataloguer quoi que ce soit avant que le travail d'opérateur ne soit terminé.

## Étape 6 : accorder le premier administrateur d'instance

Un administrateur d'instance peut voir à travers tous les comptes de l'instance, depuis le panneau d'administration d'instance. Le drapeau est accordé depuis la ligne de commande.

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Utilisez l'adresse e-mail avec laquelle vous venez de vous inscrire. La même commande avec `--revoke` retire le drapeau. Ce que le drapeau fait, et ne fait délibérément pas, est couvert dans @doc(instanceAdmin.grantAccess).

## Le résultat

Vous avez une instance fonctionnelle : l'application web répondant sur votre URL, un worker de file d'attente et un planificateur s'exécutant à ses côtés, des données dans un volume de base de données nommé, et vous-même à la fois comme propriétaire de compte et administrateur d'instance. Les membres du club peuvent désormais créer leurs propres comptes, ou vous pouvez @doc(tutorials.inviteHousehold, "inviter des personnes dans le vôtre").

## Une chose à faire avant de vous détendre

Prête à l'emploi, l'instance n'écrit les e-mails sortants que dans un fichier journal au lieu de les envoyer. Les invitations, les liens magiques, et les réinitialisations de mot de passe n'iront silencieusement nulle part tant que vous n'aurez pas configuré un vrai serveur d'envoi. C'est délibéré, et corriger cela est une tâche rapide : @doc(selfHosting.setupEmailDelivery).

## Erreurs courantes à éviter

- **Perdre la clé d'application.** Sauvegardez-la maintenant, séparément de la base de données. Sans elle, les sauvegardes sont du texte chiffré.
- **Laisser les mots de passe de substitution de la base de données.** Changez-les avant le premier démarrage, pas après.
- **Sauter la configuration des e-mails.** Le premier rapport « mon invitation n'est jamais arrivée » sera celui-ci.

## Et ensuite

- Parcourez chaque paramètre que vous avez sauté dans @doc(selfHosting.configure).
- Configurez les @doc(selfHosting.backupAndRestore, "sauvegardes") avant que le catalogue ne devienne précieux.
- Quand une nouvelle version sort, suivez @doc(selfHosting.upgrade).
