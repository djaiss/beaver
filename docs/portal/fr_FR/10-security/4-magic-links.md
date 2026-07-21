---
id: auth.magicLinks
title: Les liens magiques expliqués
slug: magic-links
section: security
---

# Les liens magiques expliqués

Un lien magique est un moyen de vous connecter sans mot de passe. Plutôt que de taper votre mot de passe, vous demandez à KolleK de vous envoyer un lien par email. Ouvrez le lien, et vous êtes connecté. Cette page explique comment cela fonctionne, quand c'est pratique, et la contrepartie que vous devez comprendre avant de vous y fier.

## Demander un lien magique

Sur la page de connexion, choisissez l'option lien magique, saisissez votre **email**, et validez. KolleK envoie un lien à usage unique à cette adresse. Ouvrez le, et vous arrivez sur votre tableau de bord.

Pour préserver votre vie privée, la page affiche la même confirmation, qu'un compte existe ou non pour l'adresse saisie, afin de ne jamais révéler qui est inscrit.

## Les règles qu'il suit

- **Le lien est valable cinq minutes.** S'il expire avant que vous ne l'ouvriez, demandez en un autre. Rien n'est perdu.
- **Il va uniquement à l'email de votre compte.** Vous devez avoir accès à cette boîte de réception. C'est aussi ce qui rend le lien sûr : seule une personne capable de lire vos emails peut l'utiliser.
- **Il fonctionne une seule fois.** Un lien qui vous a déjà connecté ne peut pas être réutilisé.

## La contrepartie avec l'authentification à deux facteurs

Se connecter avec un lien magique ne demande pas de code @doc(security.twoFactorAuth, "à deux facteurs").

C'est voulu, et non un oubli. Un lien magique prouve déjà deux choses à la fois : que la personne qui se connecte connaît votre adresse email, et qu'elle contrôle la boîte de réception associée. La boîte de réception joue le rôle du second facteur.

:::warning
Si vous utilisez l'authentification à deux facteurs, rappelez vous que quiconque contrôle votre boîte de réception peut se connecter à KolleK avec un lien magique, sans jamais voir votre authentificateur. Votre compte email est la véritable porte d'entrée ; protégez le donc avec un mot de passe fort et sa propre configuration à deux facteurs.
:::

## Quand l'utiliser

Les liens magiques vous conviennent quand.

- Vous êtes sur un appareil où vous ne voulez pas taper votre mot de passe.
- Vous avez oublié votre mot de passe et avez simplement besoin d'entrer. Une fois connecté, vous pouvez @doc(auth.resetPassword, "définir un nouveau mot de passe") depuis votre profil.
- Vous préférez ne pas utiliser de mot de passe au quotidien et votre compte email est bien protégé.

Préférez votre mot de passe et votre code d'authentification quand vous êtes sur une machine partagée ou peu fiable, où vous préférez ne pas ouvrir votre boîte de réception du tout.

## Et ensuite

- Tous les chemins de connexion réunis en un seul endroit : @doc(auth.signIn).
- Renforcez la porte d'entrée : @doc(security.twoFactorAuth).
- Le lien n'est jamais arrivé ? Voir @doc(troubleshooting.emailDelivery).
