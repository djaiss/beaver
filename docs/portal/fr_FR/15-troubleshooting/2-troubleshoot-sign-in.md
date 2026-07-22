---
id: troubleshooting.signIn
title: Dépannage de la connexion
slug: depannage-de-la-connexion
section: depannage
---

# Dépannage de la connexion

Vous êtes bloqué, ou quelque chose sur la page de connexion ne fait pas ce que vous attendiez ? Trouvez votre symptôme ci-dessous. Chaque entrée donne d'abord la solution, puis renvoie vers l'explication complète.

## J'ai oublié mon mot de passe

Utilisez le lien **mot de passe oublié** sur la page de connexion. Saisissez votre email, ouvrez l'email de réinitialisation, et choisissez un nouveau mot de passe. Le lien de réinitialisation expire après 60 minutes, utilisez-le donc rapidement, et demandez-en un autre s'il expire.

Solution plus rapide : demandez plutôt un @doc(auth.magicLinks, "lien magique"). Il vous connecte sans mot de passe, et vous pourrez définir un nouveau mot de passe ensuite depuis votre profil.

Tous les détails dans @doc(auth.resetPassword).

## Mon nouveau mot de passe est toujours refusé

KolleK exige au moins huit caractères et refuse tout mot de passe apparu dans une fuite de données publique. Ce refus concerne le mot de passe lui-même, pas votre compte. Choisissez quelque chose de plus long et unique, que vous n'avez pas utilisé ailleurs. Voir @doc(auth.resetPassword).

## J'ai perdu mon appareil d'authentification à deux facteurs

Lors de la vérification à deux facteurs, saisissez l'un de vos **codes de récupération** à la place du code à six chiffres. Chaque code de récupération ne fonctionne qu'une seule fois. Une fois connecté, désactivez puis réactivez l'authentification à deux facteurs avec votre nouvel appareil pour obtenir un nouvel appairage et un nouveau jeu de codes.

Tous les détails dans @doc(security.recoveryCodes).

:::warning
Si vous avez perdu votre authentificateur et n'avez aucun code de récupération, il n'existe aucun moyen autonome de terminer l'étape à deux facteurs. Sur une instance auto hébergée, contactez la personne qui gère votre serveur.
:::

## Mon lien magique ne fonctionne pas

Les liens magiques sont valables **cinq minutes** et fonctionnent **une seule fois**. Si le vôtre a expiré ou a déjà été utilisé, demandez-en un nouveau depuis la page de connexion. Assurez-vous d'ouvrir le lien sur l'appareil sur lequel vous souhaitez être connecté.

Tous les détails dans @doc(auth.magicLinks).

## J'ai essayé trop de fois et je suis maintenant bloqué

Les tentatives rapides et répétées sont limitées pour ralentir les essais de mots de passe. Attendez une minute et réessayez, avec attention. Si vous n'êtes pas sûr du mot de passe, passez plutôt par le @doc(auth.resetPassword, "processus de réinitialisation") ou un @doc(auth.magicLinks, "lien magique") plutôt que de continuer à deviner.

## J'ai reçu un email de « tentative de connexion échouée » que je ne reconnais pas

Quelqu'un a saisi votre email avec un mot de passe incorrect. Voir @doc(security.alertEmails) pour comprendre ce que cela signifie et quand agir.

## Mon lien d'invitation ne fonctionne pas

Deux causes courantes :

- **L'invitation a expiré.** Les invitations durent sept jours. Demandez au propriétaire du compte d'en envoyer une nouvelle.
- **Votre email possède déjà un utilisateur KolleK.** Une personne appartient à exactement un compte, une invitation ne peut donc pas être acceptée par un email qui possède déjà son propre compte.

Tous les détails dans @doc(collaboration.invitePeople).

## L'email que j'attends n'arrive jamais

Il se peut que l'email de réinitialisation, le lien magique, ou l'invitation ne vous parvienne pas. C'est généralement un problème de livraison plutôt qu'un problème de connexion. Voir @doc(troubleshooting.emailDelivery).

## Pour aller plus loin

- Les bases de chaque parcours de connexion : @doc(auth.signIn).
- Renforcez la sécurité une fois reconnecté : @doc(security.index).
