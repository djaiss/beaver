---
id: security.index
title: Aperçu de la sécurité
slug: security
section: security
---

# Aperçu de la sécurité

KolleK conserve des informations qui comptent pour vous : ce que vous possédez, sa valeur, et l'endroit où cela se trouve. Cette page recense les contrôles qui protègent votre utilisateur et vos données, afin que vous puissiez décider lesquels activer. Tous sont facultatifs. La plupart méritent cinq minutes de votre temps.

## Votre mot de passe

Chaque compte commence avec un mot de passe. KolleK applique deux règles lorsque vous en définissez un : il doit compter au moins huit caractères, et il est vérifié par rapport aux listes de mots de passe connus pour avoir fuité lors de failles passées. Si un mot de passe que vous essayez est refusé, c'est parce qu'il figure dans l'une de ces listes ; choisissez donc quelque chose que vous n'avez pas utilisé ailleurs.

Vous pouvez changer votre mot de passe à tout moment, et récupérer l'accès si vous l'oubliez. Voir @doc(auth.resetPassword).

## Authentification à deux facteurs

L'amélioration la plus importante que vous puissiez apporter. Une fois l'authentification à deux facteurs activée, la connexion avec votre mot de passe demande également un code à six chiffres provenant d'une application d'authentification sur votre téléphone. Un mot de passe volé ne suffit plus à lui seul pour entrer.

Configurez la depuis @doc(security.twoFactorAuth), et assurez vous de bien comprendre @doc(security.recoveryCodes, "les codes de récupération") avant de vous y fier.

## Codes de récupération

Lorsque vous activez l'authentification à deux facteurs, KolleK vous donne huit codes de récupération. Chacun peut être utilisé une seule fois, à la place d'un code d'authentification, pour vous permettre de revenir si vous perdez votre téléphone. Conservez les dans un endroit sûr. @doc(security.recoveryCodes) explique comment procéder.

## Liens magiques

Un moyen de vous connecter sans mot de passe. KolleK vous envoie par email un lien qui vous connecte directement, valable cinq minutes. Pratique, avec une contrepartie à bien comprendre : un lien magique ne demande pas de code à deux facteurs, car l'accès à votre boîte de réception joue déjà le rôle de second facteur. @doc(auth.magicLinks) explique quand les utiliser.

## Clés API

Si vous utilisez l'API KolleK, vous vous authentifiez avec des clés API personnelles. Elles sont créées et révoquées depuis votre profil, et KolleK vous envoie un email à chaque création ou suppression, afin qu'une clé que vous n'avez pas créée ne passe jamais inaperçue. Voir @doc(apiKeys.manage).

## Emails d'alerte

KolleK surveille les événements qui méritent d'être portés à votre connaissance : une tentative de connexion échouée, une connexion depuis un nouvel appareil, un changement d'adresse IP, une clé API créée ou supprimée. Lorsque l'un de ces événements se produit, vous recevez un email. @doc(security.alertEmails) explique ce que signifie chaque alerte et ce qu'il faut faire.

## Une configuration raisonnable

Si vous ne faites que deux choses, faites celles ci.

1. Activez l'@doc(security.twoFactorAuth, "authentification à deux facteurs").
2. Enregistrez vos @doc(security.recoveryCodes, "codes de récupération") ailleurs que sur votre téléphone.

Tout le reste dans cette section peut attendre que vous en ayez besoin.

## Pages de cette section

1. @doc(security.twoFactorAuth)
2. @doc(security.recoveryCodes)
3. @doc(auth.magicLinks)
4. @doc(auth.resetPassword)
5. @doc(security.alertEmails)
6. @doc(apiKeys.manage)
