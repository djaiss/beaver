---
id: troubleshooting.featureStatus
title: État des fonctionnalités et feuille de route
slug: etat-des-fonctionnalites
section: depannage
---

# État des fonctionnalités et feuille de route

KolleK est en pleine évolution, et certaines fonctionnalités sont visibles avant d'être terminées. Cette page est la seule liste honnête de ce qui est pleinement disponible aujourd'hui et de ce qui reste à venir, afin qu'aucune autre page n'ait à nuancer ses propos. Quand le produit évolue, cette page évolue avec lui.

## Disponible aujourd'hui

Tout le reste documenté dans ce portail fonctionne comme décrit, notamment :

- Les collections, objets, exemplaires, photos, étiquettes, catégories, collections à compléter et séries.
- Les types de collection avec champs personnalisés, y compris l'import et l'export des définitions de types au format JSON.
- L'historique complet des exemplaires : transactions, évaluations, assurance, prêts, entretien, provenance, historique des emplacements et documents, avec la chronologie unifiée.
- La collaboration avec les rôles propriétaire, éditeur et lecteur, et les invitations par email.
- L'authentification à deux facteurs, les liens magiques, les clés API et les emails d'alerte de sécurité.
- L'API JSON complète avec sa référence générée sur `/docs/api`.
- L'auto hébergement avec Docker, le chiffrement des données au repos, la corbeille avec restauration, et les statistiques par collection.

## Pas encore

### Recherche globale

Le champ de recherche du tableau de bord est un espace réservé et ne recherche encore rien. Ce qui fonctionne aujourd'hui : le filtrage des objets d'une collection ouverte (voir @doc(collections.chooseView)), et la recherche dans la @doc(photos.library, "bibliothèque de photos").

### Visibilité et partage des collections

Chaque collection possède un paramètre de visibilité (privée, partagée ou publique), et ce paramètre est enregistré, mais il n'est pas encore appliqué. Chaque membre d'un compte peut toujours parcourir toutes les collections qu'il contient, et il n'existe aucun lien public, donc une collection marquée publique n'est absolument pas accessible depuis l'extérieur du compte. Définissez la visibilité dès maintenant pour enregistrer votre intention ; elle prendra effet à l'arrivée du partage. Voir @doc(sharing.overview).

### Livraison des webhooks

Vous pouvez enregistrer des points de terminaison de webhook, et chacun reçoit un secret de signature, mais aucun événement de l'application ne déclenche encore de webhook. Le mécanisme de signature et de livraison est en place, en attente que les événements soient connectés. Configurez-les dès maintenant si vous le souhaitez ; les livraisons arriveront à mesure que le domaine se développe. Voir @doc(webhooks.overview).

### Import et export des objets et des collections

L'import et l'export n'existent que pour les définitions de types de collection. Il n'existe pas encore d'import ou d'export au niveau des objets ou d'une collection entière. Pour tout récupérer, les instances auto hébergées disposent de sauvegardes complètes ; voir @doc(dataSafety.backupCollectionData).

### Administration de l'instance : Support et Avis

Dans le panneau d'administration de l'instance, les sections Support et Avis sont des espaces réservés qui l'indiquent clairement. Le reste du panneau fonctionne ; voir @doc(instanceAdmin.panel).

## Comment lire cette page

Rien ici n'est une promesse avec une date. « Pas encore » signifie que les bases techniques existent peut-être, mais que vous ne devez pas planifier autour de cette fonctionnalité tant qu'elle n'a pas rejoint la liste ci-dessus. En cas de doute, faites confiance à cette page plutôt qu'à toute autre indication qui semblerait dire le contraire.

Les questions auxquelles cette page ne répond pas se trouvent probablement dans la @doc(troubleshooting.faq, "FAQ").
