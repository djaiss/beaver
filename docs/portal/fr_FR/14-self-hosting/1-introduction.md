---
id: selfHosting.index
title: Aperçu de l'auto-hébergement
slug: auto-hebergement
section: auto-hebergement
---

# Aperçu de l'auto-hébergement

Faire fonctionner votre propre instance KolleK est une manière d'utiliser le produit à part entière, prise en charge, et gratuite. Cette page vous explique ce que cela implique avant que vous installiez quoi que ce soit, et vous donne la règle qui compte plus que toutes les autres.

Si vous n'avez pas encore choisi entre l'auto-hébergement et une instance hébergée, commencez par @doc(kollek.hostingOptions).

## Ce qu'implique la gestion d'une instance

KolleK est distribué sous la forme d'une seule image Docker qui joue trois rôles, choisis par une variable d'environnement :

- Le rôle **web** sert l'application elle-même.
- Le rôle **queue** traite les tâches en arrière-plan (envoi d'e-mails, livraisons de webhooks, journalisation).
- Le rôle **scheduler** exécute les tâches de maintenance quotidiennes.

Le fichier Docker Compose fourni démarre les trois, plus une base de données MySQL. Les sessions, le cache et la file d'attente reposent tous sur la base de données, il n'y a donc pas de Redis ni d'autre service supplémentaire à exploiter. Les photos et documents téléversés vivent sur un volume de stockage, sur disque local par défaut, avec la possibilité d'utiliser un stockage compatible S3.

Les prérequis sont modestes : une machine avec Docker Engine 24 ou plus récent et le plugin Compose. Un petit serveur virtuel fait tourner confortablement une instance personnelle.

## La règle à retenir dès maintenant

KolleK chiffre les données sensibles au repos à l'aide de la clé d'application de votre instance.

:::warning
Définissez la clé d'application une seule fois, avant le premier démarrage, et ne la changez jamais sur une instance en fonctionnement. Si la clé change, chaque champ chiffré et chaque session devient définitivement illisible. Traitez la clé comme les données elles-mêmes : sauvegardez-la, et gardez-la identique sur tous les conteneurs.
:::

Ce point mérite d'être bien compris avant l'installation. @doc(selfHosting.applicationKeyAndEncryption) explique ce que la clé protège, comment la conserver, et la seule façon sûre de la faire tourner délibérément.

## Vos responsabilités

L'auto-hébergement fait de vous l'opérateur. Concrètement, cela signifie :

- **L'installation et les mises à jour.** Les deux sont des procédures Docker courtes et documentées.
- **Les sauvegardes.** Il n'existe pas de sauvegarde automatisée intégrée à l'application. Vous sauvegardez vous-même la base de données et le volume de stockage, ainsi que la clé d'application.
- **La livraison des e-mails.** Une instance fraîchement installée journalise les e-mails au lieu de les envoyer, si bien que les invitations et les liens de connexion ne vont nulle part tant que vous n'avez pas configuré un service d'envoi.
- **Maintenir les trois rôles en fonctionnement.** En particulier, les tâches en arrière-plan et la maintenance quotidienne s'arrêtent silencieusement si les conteneurs queue ou scheduler sont éteints.

Alex, qui gère une instance pour son club de collectionneurs, y consacre quelques minutes par mois une fois la configuration initiale terminée. Ce n'est pas une charge opérationnelle lourde, mais elle vous incombe.

## Cette section

Parcourez les pages à peu près dans cet ordre :

1. @doc(selfHosting.installDocker). Partir de rien pour obtenir une instance en fonctionnement.
2. @doc(selfHosting.configure). Les variables d'environnement que vous manipulerez réellement.
3. @doc(selfHosting.setupEmailDelivery). Faire en sorte que les invitations et les liens magiques soient réellement envoyés.
4. @doc(selfHosting.applicationKeyAndEncryption). La règle opérationnelle la plus importante.
5. @doc(selfHosting.upgrade). Passer à une nouvelle version en toute sécurité.
6. @doc(selfHosting.backupAndRestore). Protéger les données.
7. @doc(selfHosting.scheduledJobs). Ce que l'application fait toute seule chaque nuit.
8. @doc(instanceAdmin.grantAccess). Amorcer l'administrateur à l'échelle du serveur.
9. @doc(instanceAdmin.panel). Ce que cet administrateur peut voir et faire.
10. @doc(selfHosting.cliCommands). Les commandes artisan dont un opérateur a besoin.
11. @doc(selfHosting.addLanguage). Comment l'interface est traduite.

## Et ensuite

- Prêt à installer ? Rendez-vous sur @doc(selfHosting.installDocker).
- Vous préférez un parcours guidé de bout en bout ? Suivez le @doc(tutorials.selfHostWithDocker, "tutoriel d'auto-hébergement").
