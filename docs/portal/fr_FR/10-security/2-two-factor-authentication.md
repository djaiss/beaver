---
id: security.twoFactorAuth
title: Protégez votre compte avec l'authentification à deux facteurs
slug: two-factor-authentication
section: security
---

# Protégez votre compte avec l'authentification à deux facteurs

L'authentification à deux facteurs ajoute une étape supplémentaire à la connexion. Une fois votre mot de passe accepté, KolleK demande un code à six chiffres provenant d'une application d'authentification sur votre téléphone. Même si quelqu'un découvre votre mot de passe, il ne peut pas entrer sans ce code.

C'est le contrôle de sécurité le plus efficace proposé par KolleK, et sa mise en place prend quelques minutes.

## Ce dont vous aurez besoin

Une application d'authentification sur votre téléphone, comme n'importe quelle application prenant en charge les codes à usage unique basés sur le temps. Si vous avez déjà scanné un QR code pour protéger un autre compte, vous en avez déjà une.

## L'activer

::::steps
:::step title="Ouvrez vos paramètres de sécurité"
Rendez vous sur votre profil et ouvrez la zone de sécurité, puis choisissez de configurer l'**authentification à deux facteurs**.
:::

:::step title="Scannez le QR code"
KolleK affiche un QR code. Ouvrez votre application d'authentification, ajoutez un nouveau compte, et scannez le code. L'application se met à afficher pour KolleK un code à six chiffres qui change toutes les 30 secondes.

::screenshot{label="Écran de configuration de l'authentification à deux facteurs avec le QR code"}
:::

:::step title="Confirmez avec un code"
Saisissez le code à six chiffres actuel de votre application dans le champ de confirmation et validez. Cela prouve que l'application et KolleK sont synchronisés avant que quoi que ce soit ne change dans votre façon de vous connecter.
:::

:::step title="Enregistrez vos codes de récupération"
KolleK génère huit codes de récupération. Copiez les dans un endroit sûr autre que votre téléphone, comme un gestionnaire de mots de passe ou une page imprimée. Chaque code peut vous permettre de vous connecter une fois si vous perdez un jour votre authentificateur.

::screenshot{label="Les huit codes de récupération affichés après la configuration"}
:::
::::

:::warning
Si vous perdez votre authentificateur et n'avez aucun code de récupération, vous ne pouvez pas terminer l'étape à deux facteurs, et vous risquez de vous retrouver bloqué hors de votre utilisateur. Enregistrez les codes avant de fermer la page.
:::

## Ce qui change lors de la connexion

Désormais, se connecter avec votre email et votre mot de passe demande une étape supplémentaire. Une fois votre mot de passe accepté, KolleK demande le code actuel de votre application d'authentification. Saisissez le et vous êtes connecté.

Si vous ne pouvez pas accéder à votre application, saisissez plutôt l'un de vos @doc(security.recoveryCodes, "codes de récupération").

:::note
Se connecter avec un @doc(auth.magicLinks, "lien magique") ne demande pas de code à deux facteurs. L'accès à votre boîte de réception joue déjà le rôle de second facteur, protégez la donc en conséquence.
:::

## La désactiver

Vous pouvez désactiver l'authentification à deux facteurs depuis la même zone de sécurité. Cela supprime l'étape du code lors de la connexion, et supprime aussi vos codes de récupération ainsi que l'association avec votre application d'authentification. Si vous la réactivez plus tard, vous scannerez un nouveau QR code et recevrez un nouveau jeu de codes de récupération.

## Et ensuite

- Assurez vous que votre solution de secours fonctionne : @doc(security.recoveryCodes).
- Comprenez le parcours sans mot de passe et sa contrepartie : @doc(auth.magicLinks).
- Découvrez tous les moyens d'accéder à l'application : @doc(auth.signIn).
