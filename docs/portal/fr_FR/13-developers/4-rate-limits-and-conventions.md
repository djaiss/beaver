---
id: api.rateLimitsAndConventions
title: Limites de débit et conventions
slug: rate-limits-and-conventions
section: developers
---

# Limites de débit et conventions

Quelques conventions s'appliquent à l'ensemble de l'API. Les apprendre une bonne fois vous évite des surprises sur chaque point d'accès, c'est pourquoi elles sont regroupées ici plutôt que répétées dans toute la référence.

## Limites de débit

- Les requêtes authentifiées sont limitées à **60 par minute** par utilisateur.
- `POST /api/register` et `POST /api/login` sont limités à **6 par minute**, ce qui protège contre le bourrage d'identifiants.

Lorsque vous dépassez une limite, l'API répond avec un code HTTP 429. Patientez un instant avant de réessayer. Si vous écrivez un import en masse, régulez vos requêtes plutôt que de les envoyer aussi vite que possible, et rappelez vous que l'API traite un objet par requête, car il n'existe pas de points d'accès en masse.

## Pagination

Les points d'accès de liste sont paginés et partagent une même enveloppe :

- `data` contient la page de ressources.
- `links` contient les URL `first`, `last`, `prev` et `next`.
- `meta` contient la page courante, le nombre total, et d'autres détails associés.

Les pages contiennent **10 ressources par défaut**. Demandez en davantage avec le paramètre de requête `per_page`, jusqu'à un **maximum de 100**. Suivez `links.next` jusqu'à ce qu'il soit `null` pour parcourir une liste entière.

## Les montants sont exprimés dans la plus petite unité monétaire

Chaque montant dans l'API (valeurs estimées, montants de transaction, dépôts, valeurs assurées) est un entier exprimé dans la plus petite unité de sa devise. Pour les dollars et les euros, cela signifie des centimes : un achat de 49,99 $ circule sous la forme `4999`. Cela évite entièrement les arrondis en virgule flottante. Convertissez pour l'affichage dans votre propre code, et rappelez vous que chaque @doc(collections.overview, "collection") porte sa propre devise.

## Un accès interdit répond comme une absence

L'API applique les mêmes @doc(accounts.usersAndRoles, "rôles") que l'application web, avec une nuance volontaire : une action que vous n'êtes pas autorisé à effectuer, ou une ressource d'un autre compte, répond **404 Not Found**, et non 403 Forbidden. Un appelant ne peut pas distinguer « cela n'existe pas » de « cela ne vous appartient pas », donc l'API ne confirme jamais ce qui existe en dehors de votre compte.

:::note
Si un point d'accès retourne de façon inattendue un 404 sur un objet que vous voyez dans l'application, vérifiez le rôle de l'utilisateur dont vous utilisez le jeton. Le jeton d'un lecteur reçoit un 404 sur chaque écriture.
:::

## Erreurs et validation

Une validation échouée répond avec un code HTTP 422, un champ `message` et un objet `errors` indexé par nom de champ. Les autres erreurs suivent la sémantique HTTP habituelle : 401 lorsque le jeton est manquant ou révoqué, 404 comme décrit ci dessus, 429 pour les limites de débit.

## Où aller ensuite

- Voyez ces conventions appliquées sur des points d'accès réels dans la référence générée sur `/docs/api`.
- Prêt pour la livraison d'événements un jour ? Découvrez où en sont les @doc(webhooks.overview).
