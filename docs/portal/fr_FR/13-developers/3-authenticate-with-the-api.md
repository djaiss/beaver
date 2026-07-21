---
id: api.authenticate
title: S'authentifier auprès de l'API
slug: authenticate-with-the-api
section: developers
---

# S'authentifier auprès de l'API

Chaque requête API est authentifiée avec un jeton porteur. Cette page vous accompagne depuis le début jusqu'à votre première requête réussie, puis couvre l'obtention de jetons via l'API elle-même et leur révocation.

Remplacez `https://kollek.example.com` dans les exemples par l'adresse de votre instance. L'API se trouve sous `/api` à cette adresse.

## Le chemin le plus rapide : créer une clé dans l'application

Le moyen le plus simple d'obtenir un jeton est de créer une clé API depuis votre profil.

::::steps
:::step title="Créer une clé API"
Dans l'application, ouvrez les paramètres de votre profil et allez dans **Clés API**. Créez une clé et donnez-lui un libellé que vous reconnaîtrez plus tard, comme « Script de reporting ».

::screenshot{label="Paramètres du profil, page des clés API avec le formulaire de nouvelle clé"}
:::

:::step title="Copier le jeton"
Le jeton s'affiche une seule fois, juste après sa création. Copiez-le immédiatement et conservez-le en lieu sûr, comme un gestionnaire de mots de passe. Si vous le perdez, révoquez la clé et créez-en une nouvelle.
:::

:::step title="Effectuer votre première requête"
Envoyez le jeton dans l'en-tête `Authorization`. Un bon premier appel est `/api/me`, qui retourne votre propre utilisateur :

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

Si vous recevez un document JSON décrivant votre utilisateur, vous êtes authentifié. La création et la révocation de clés, ainsi que la consultation de leur dernière utilisation, sont couvertes dans @doc(apiKeys.manage).

:::note
Les jetons n'expirent pas d'eux-mêmes. Ils fonctionnent jusqu'à ce que vous les révoquiez, alors traitez un jeton comme un mot de passe.
:::

## Obtenir un jeton via l'API

Vous pouvez aussi vous authentifier entièrement en HTTP, ce qui convient aux scripts et intégrations qui gèrent leurs propres identifiants.

Connectez-vous avec votre adresse e-mail et votre mot de passe pour recevoir un jeton :

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

La réponse contient votre jeton sous `data.token`. Le champ optionnel `device_name` nomme le jeton pour que vous puissiez le reconnaître plus tard dans votre liste de clés.

Deux choses à savoir :

- Si @doc(security.twoFactorAuth, "l'authentification à deux facteurs") est activée sur votre utilisateur, le point d'accès de connexion exige aussi un champ `code` contenant un code TOTP actuel de votre application d'authentification, ou l'un de vos @doc(security.recoveryCodes, "codes de récupération").
- S'inscrire via l'API fonctionne aussi : `POST /api/register` crée un utilisateur avec son propre compte et retourne un jeton, exactement comme une inscription dans le navigateur.

Les deux points d'accès sont limités à 6 requêtes par minute, ce qui est largement suffisant pour de vraies connexions et arrête les tentatives de force brute.

## Révoquer des jetons

Vous avez deux options :

- `DELETE /api/logout` révoque le jeton qui a effectué la requête. Utilisez ceci lorsqu'un script termine avec un jeton temporaire.
- La page **Clés API** de votre profil liste tous les jetons et peut en révoquer n'importe lequel. Les points d'accès des clés API dans la référence générée font la même chose en HTTP.

KolleK vous envoie un e-mail lorsqu'une clé est créée ou supprimée depuis l'application, afin qu'une activité inattendue sur une clé ne passe pas inaperçue. Voir @doc(security.alertEmails).

## Où aller ensuite

- Apprenez les conventions de requête dans @doc(api.rateLimitsAndConventions).
- Gérez vos jetons dans @doc(apiKeys.manage).
- Explorez chaque point d'accès dans la référence générée sur `/docs/api`.
