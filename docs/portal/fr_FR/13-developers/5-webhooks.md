---
id: webhooks.overview
title: Webhooks
slug: webhooks
section: developers
---

# Webhooks

Les webhooks permettent à un système externe de recevoir un appel HTTP de KolleK lorsque quelque chose se produit dans votre compte. Vous pouvez les configurer dès aujourd'hui, et cette page montre comment faire. Mais lisez le paragraphe suivant d'abord, car il encadre tout le reste.

:::note
Aucun événement applicatif ne déclenche actuellement de webhook. Les mécanismes d'enregistrement, de signature et de livraison sont en place et testés, mais les événements ne commenceront à se déclencher qu'à mesure que le domaine des collections se développe. Configurez votre récepteur dès maintenant si vous le souhaitez, mais n'attendez rien de lui pour l'instant. La @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités") suit l'évolution de cette situation.
:::

## Ce qui existe aujourd'hui

Enregistrer un point d'accès stocke une URL de destination avec son propre secret de signature. Lorsque KolleK finira par déclencher des événements, chacun sera livré à tous les points d'accès actifs que vous avez enregistrés, signé afin que votre récepteur puisse vérifier qu'il provient bien de votre instance.

Les points d'accès de webhook appartiennent à votre utilisateur, pas à l'ensemble du compte.

## Enregistrer un point d'accès

Depuis l'application, ouvrez les paramètres de votre profil et allez dans **Webhooks**. Ajoutez l'URL que votre récepteur écoute, avec un libellé pour vous souvenir de son usage. Chaque point d'accès reçoit son propre secret de signature, une chaîne de 64 caractères générée à la création du point d'accès. Conservez le avec votre récepteur.

Un opérateur peut aussi créer un point d'accès depuis la ligne de commande :

```bash
php artisan beaver:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

La commande affiche l'identifiant du point d'accès et son secret de signature.

## La charge utile que votre récepteur doit attendre

Chaque livraison est un `POST` JSON de cette forme :

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` nomme ce qui s'est produit. Aucun nom d'événement n'est encore défini.
- `happened_at` est un horodatage ISO 8601 du moment où c'est arrivé.
- `data` porte la charge utile de cet événement.

## Vérifier les signatures

Chaque livraison inclut un en-tête `Signature` : un hachage HMAC SHA256 du corps brut de la requête, calculé avec le secret de signature de votre point d'accès. Recalculez le même hachage de votre côté et comparez. S'ils diffèrent, rejetez la requête, car elle ne provient pas de votre instance.

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## Livraison et nouvelles tentatives

Les livraisons sont mises en file d'attente et envoyées en arrière plan. Une livraison qui échoue est retentée jusqu'à 3 fois avec un délai exponentiel croissant. Votre récepteur doit répondre rapidement avec un statut 2xx et effectuer son véritable travail de façon asynchrone.

Sur une instance autohébergée, les livraisons s'exécutent sur le worker de file d'attente, le rôle queue doit donc être actif. Voir @doc(selfHosting.installDocker).

## Où aller ensuite

- Vérifiez ce qui est actif et ce qui est en attente sur la @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités").
- Développez avec l'API en attendant, en commençant par @doc(api.authenticate).
