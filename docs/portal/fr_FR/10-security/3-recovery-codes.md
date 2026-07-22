---
id: security.recoveryCodes
title: Enregistrez et utilisez vos codes de récupération
slug: codes-de-recuperation
section: securite
---

# Enregistrez et utilisez vos codes de récupération

Les codes de récupération sont votre moyen de revenir si vous perdez votre authentificateur. Lorsque vous activez l'@doc(security.twoFactorAuth, "authentification à deux facteurs"), KolleK en génère huit. Chaque code fonctionne exactement une fois, à la place d'un code provenant de votre application.

Les téléphones se perdent, se cassent et se remplacent. Les codes de récupération sont ce qui se dresse entre cette mauvaise journée ordinaire et le fait de vous retrouver bloqué hors de votre catalogue.

## Où les obtenir

Les codes s'affichent juste après la confirmation de la configuration à deux facteurs. C'est à ce moment là que vous devez les enregistrer.

Bons endroits pour les conserver.

- Un gestionnaire de mots de passe, dans les notes de votre entrée KolleK.
- Une page imprimée dans un tiroir chez vous.
- Un fichier chiffré que vous sauvegardez.

Un mauvais endroit pour les conserver est votre téléphone seul, car la situation où vous en avez besoin est justement celle où votre téléphone a disparu.

:::warning
Si vous perdez à la fois votre authentificateur et vos codes de récupération, vous ne pouvez pas terminer l'étape à deux facteurs, et vous risquez de vous retrouver bloqué hors de votre utilisateur. Il n'existe aucun moyen en libre service de contourner cela, alors conservez les codes en lieu sûr dès maintenant.
:::

## Utiliser un code pour vous connecter

Lorsque KolleK vous demande votre code d'authentification à six chiffres et que vous ne pouvez pas en fournir un.

1. Sur l'écran du défi à deux facteurs, saisissez l'un de vos codes de récupération à la place du code de l'application.
2. Vous êtes connecté normalement.

C'est tout ce qu'il y a à savoir. Le défi accepte aussi bien un code d'authentification actuel qu'un code de récupération non utilisé.

## Chaque code fonctionne une fois

Un code de récupération est consommé dès que vous l'utilisez. Il ne fonctionnera plus jamais, et vos codes restants demeurent valides. Rayez les codes utilisés là où vous les avez enregistrés.

:::note
Si vous êtes à court de codes, ou si vous soupçonnez que quelqu'un d'autre les a vus, désactivez l'authentification à deux facteurs puis réactivez la. La réactivation génère un nouveau jeu de huit codes et invalide les anciens.
:::

## Une fois de retour dans votre compte

Si vous avez utilisé un code de récupération parce que vous avez perdu votre authentificateur pour de bon, prenez deux minutes pour remettre les choses en ordre : désactivez l'authentification à deux facteurs depuis vos paramètres de sécurité, puis réactivez la avec votre nouvel appareil. Vous obtiendrez un nouveau QR code à scanner et un nouveau jeu de codes de récupération à enregistrer.

## Et ensuite

- Configurez ou réinitialisez l'étape du code elle même : @doc(security.twoFactorAuth).
- Bloqué d'une autre façon ? Voir @doc(troubleshooting.signIn).
