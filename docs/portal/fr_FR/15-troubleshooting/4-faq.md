---
id: troubleshooting.faq
title: Foire aux questions
slug: faq
section: troubleshooting
---

# Foire aux questions

Des réponses courtes aux questions qui reviennent sans cesse. Chacune renvoie vers la page qui traite le sujet en détail.

## Quelle est la différence entre un objet et un exemplaire ?

Un objet est le type de chose, comme « Amazing Spider-Man #1 ». Un exemplaire est une instance physique que vous possédez réellement. Si vous possédez trois exemplaires de la même bande dessinée, c'est un objet avec trois exemplaires, chacun ayant son propre état, son propre lieu de stockage, sa propre valeur et son propre historique. C'est l'idée la plus importante de KolleK. Voir @doc(items.itemsVsCopies).

## Puis-je appartenir à plusieurs comptes ?

Non. Un utilisateur appartient à exactement un compte, et une adresse email ne peut avoir qu'un seul utilisateur. Cela signifie aussi qu'une invitation vers le compte de quelqu'un d'autre ne peut pas être acceptée par un email qui possède déjà son propre compte. Voir @doc(accounts.usersAndRoles).

## KolleK est-il vraiment gratuit ?

Oui. Il n'y a aucune facturation dans l'application : pas de forfaits, pas de niveaux, pas de fonctionnalités payantes. L'auto hébergement est gratuit, et toutes les fonctionnalités sont incluses quelle que soit la façon dont vous l'exécutez. Voir @doc(kollek.hostingOptions).

## Comment récupérer mes données ?

Aujourd'hui, depuis l'application, vous pouvez exporter @doc(collectionTypes.importExport, "les définitions de types de collection au format JSON"). Il n'existe pas encore d'export d'objets ni de collection entière. La réponse complète pour les instances auto hébergées est une sauvegarde au niveau de l'instance, de la base de données et des fichiers téléversés, couverte dans @doc(selfHosting.backupAndRestore). Le résumé honnête se trouve dans @doc(dataSafety.backupCollectionData).

## Pourquoi ne puis-je pas retirer ou rétrograder le dernier propriétaire ?

Un compte doit toujours conserver au moins un propriétaire, sinon personne ne pourrait le gérer, inviter des membres, ou le supprimer. Promouvez d'abord quelqu'un d'autre au rang de propriétaire. Voir @doc(collaboration.manageMembersAndRoles).

## Où se trouve la fonctionnalité de recherche ?

La recherche globale depuis le tableau de bord n'est pas encore disponible ; le champ que vous y voyez est un espace réservé. Ce qui fonctionne aujourd'hui : le filtrage au sein d'une collection ouverte, et la recherche dans votre bibliothèque de photos. Voir @doc(troubleshooting.featureStatus).

## Les webhooks fonctionnent-ils déjà ?

En partie. Vous pouvez enregistrer des points de terminaison et chacun reçoit un secret de signature, mais aucun événement de l'application ne déclenche encore de webhook. Le mécanisme de livraison est prêt ; les événements arriveront à mesure que le produit évoluera. Voir @doc(webhooks.overview).

## Mes données sont-elles chiffrées, et que cela protège-t-il ?

Les champs sensibles sont chiffrés au repos dans la base de données avec la clé de votre instance. Cela protège le contenu de la base de données si elle seule est dérobée. Ce n'est pas un chiffrement de bout en bout : la personne qui gère l'instance détient la clé et peut accéder aux données. Voir @doc(dataSafety.howProtected).

## Puis-je ajouter mes propres états ?

Pas encore depuis l'application web. Les états préremplis (New, Like New, Used, Worn, Damaged) apparaissent sous forme de listes déroulantes partout, et ajouter ou renommer des états n'est actuellement possible que via l'API. Voir @doc(conditions.overview) et @doc(troubleshooting.featureStatus).

## Un élément a été supprimé. Puis-je le récupérer ?

S'il s'agissait d'une collection, d'un objet, d'un exemplaire, d'une catégorie ou d'une collection à compléter, il est allé dans la corbeille et peut être restauré pendant 30 jours par défaut. Les photos, documents et enregistrements d'historique sont supprimés immédiatement et ne peuvent pas être récupérés depuis l'application. Voir @doc(dataSafety.restoreFromTrash).

## Toujours bloqué ?

- Problèmes de connexion : @doc(troubleshooting.signIn).
- Emails manquants : @doc(troubleshooting.emailDelivery).
- Ce qui est terminé et ce qui ne l'est pas : @doc(troubleshooting.featureStatus).
