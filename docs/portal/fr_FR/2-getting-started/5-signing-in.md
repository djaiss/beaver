---
id: auth.signIn
title: Se connecter
slug: signing-in
section: getting-started
---

# Se connecter

KolleK vous propose plusieurs façons de vous connecter. Cette page les présente une par une afin que vous puissiez choisir celle qui vous convient, et vous indique où aller si vous êtes bloqué.

## Se connecter avec email et mot de passe

La méthode habituelle. Allez sur la page de connexion, saisissez l'**email** et le **mot de passe** avec lesquels vous vous êtes inscrit, et validez. Vous arrivez sur votre tableau de bord.

Si @doc(security.twoFactorAuth, "l'authentification à deux facteurs") est activée pour votre compte, un code vous sera demandé juste après votre mot de passe. Voir ci dessous.

## Se connecter avec un lien magique

Si vous préférez ne pas saisir de mot de passe, KolleK peut vous envoyer par email un lien qui vous connecte.

Sur la page de connexion, choisissez l'option lien magique, saisissez votre **email**, et validez. KolleK envoie un lien à usage unique à cette adresse. Ouvrez le, et vous êtes connecté.

Deux choses à savoir :

- **Le lien est valable cinq minutes.** S'il expire, il suffit d'en demander un autre.
- **Le lien est envoyé à l'email de votre compte**, vous avez donc besoin d'accéder à cette boîte de réception. C'est aussi ce qui le rend sûr : seule une personne pouvant lire votre email peut l'utiliser.

## L'étape de la double authentification

Si vous avez activé l'authentification à deux facteurs, vous connecter avec votre mot de passe nécessite une étape supplémentaire. Une fois votre mot de passe accepté, KolleK vous demande le code actuel de votre application d'authentification. Saisissez le pour terminer la connexion.

Si vous ne pouvez pas accéder à votre application d'authentification, vous pouvez saisir l'un de vos @doc(security.recoveryCodes, "codes de récupération") à la place. Chaque code de récupération fonctionne une seule fois.

:::warning
Se connecter avec un lien magique ne demande pas de code à deux facteurs, car l'accès à votre boîte email joue déjà le rôle de second facteur. Si vous comptez sur l'authentification à deux facteurs, gardez cela en tête lorsque vous choisissez comment vous connecter, et protégez votre compte email en conséquence.
:::

La mise en place de l'authentification à deux facteurs et l'enregistrement des codes de récupération sont traités dans la section **Sécurité** de cette documentation.

## Mot de passe oublié

Si vous ne vous souvenez plus de votre mot de passe, utilisez le lien "mot de passe oublié" sur la page de connexion. Saisissez votre email, et KolleK vous envoie un lien de réinitialisation.

Pour votre confidentialité, KolleK affiche toujours le même message de confirmation, qu'un compte existe ou non pour cette adresse, afin que la page ne révèle pas qui est inscrit. Si vous avez un compte, l'email de réinitialisation arrivera. Si vous utilisez un lien magique pour vous reconnecter, vous pourrez réinitialiser votre mot de passe ensuite depuis votre profil.

## Et ensuite

- Nouveau ici et encore en train de vous installer ? Retournez à @doc(gettingStarted.checklist).
- Vous voulez une protection plus forte ? Activez l'authentification à deux facteurs depuis la section **Sécurité**.
