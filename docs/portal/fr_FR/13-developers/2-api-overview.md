---
id: api.overview
title: Vue d'ensemble de l'API
slug: api-overview
section: developers
---

# Vue d'ensemble de l'API

L'API KolleK est une API JSON qui reflète l'application web à l'identique. Chaque fonctionnalité de l'application (créer des collections, ajouter des objets et des exemplaires, enregistrer des transactions, gérer les membres) possède un point d'accès correspondant, soumis exactement aux mêmes règles. Si votre rôle vous permet de faire quelque chose dans le navigateur, votre jeton vous permet de le faire en HTTP. S'il ne le permet pas, l'API refuse de la même façon que le ferait l'application.

Cette page vous donne le modèle mental. La référence complète et toujours à jour des points d'accès est générée à partir du code et servie par votre instance :

- `/docs/api` pour la référence consultable.
- `/docs/api.md` pour l'ensemble de la référence au format Markdown.
- `/docs/api/{section}.md` pour une seule section au format Markdown, pratique pour transmettre un seul sujet à un outil.

:::note
Sur une instance autohébergée, la référence fait partie du site vitrine public, désactivé par défaut. Un opérateur l'active avec le paramètre `SHOW_MARKETING_SITE`. Voir @doc(selfHosting.configure).
:::

## Limitée à votre compte

L'API est cloisonnée par locataire. Un jeton appartient à un utilisateur, et un utilisateur appartient à exactement un @doc(accounts.usersAndRoles, "compte"), donc chaque requête se résout à travers ce compte. Vous ne pouvez pas atteindre les données d'un autre compte, et vous ne transmettez d'identifiant de compte nulle part. Il n'y a rien à configurer : authentifiez-vous, et vous êtes dans votre propre espace de travail.

Les mêmes @doc(accounts.usersAndRoles, "rôles") s'appliquent que dans l'application. Le jeton d'un lecteur peut lire mais pas écrire. Le jeton d'un éditeur peut gérer le contenu du catalogue. Les actions réservées au propriétaire (membres, paramètres du compte) nécessitent le jeton d'un propriétaire.

## Comment les ressources sont structurées

Les ressources s'imbriquent de la façon dont @doc(kollek.howOrganized, "KolleK est organisé") :

- Votre **compte** contient les ressources à l'échelle du compte : membres, types de collections, champs personnalisés, étiquettes, emplacements, états.
- Les **collections** contiennent des **objets**, ainsi que des catégories et des ensembles.
- Les **objets** contiennent des **photos** et des **exemplaires**.
- Les **exemplaires** portent les ressources d'historique : transactions, estimations, assurances, prêts, entretiens, événements de provenance, historique des emplacements, documents, et la chronologie combinée.

Les réponses suivent globalement la forme JSON:API : chaque ressource revient sous la forme `type`, `id`, `attributes` et `links`. Les listes sont paginées avec une enveloppe standard, décrite dans @doc(api.rateLimitsAndConventions).

## Ce que cette section couvre

Ces pages couvrent la prise en main et les concepts que la référence générée ne peut pas enseigner : l'authentification, les conventions et l'état actuel des webhooks. Pour un point d'accès précis, ses paramètres, et des exemples de requêtes et réponses détaillés, allez directement sur `/docs/api`.

:::note
Il n'existe pas de mode test. Chaque requête API s'exécute sur votre compte réel, alors soyez prudent avec les appels destructeurs pendant vos essais.
:::

## Où aller ensuite

- Effectuez votre première requête dans @doc(api.authenticate).
- Parcourez @doc(api.rateLimitsAndConventions) avant d'écrire un client.
- Explorez la référence générée sur `/docs/api` de votre instance.
