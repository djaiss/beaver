---
id: auth.resetPassword
title: Réinitialisez votre mot de passe
slug: reinitialiser-votre-mot-de-passe
section: securite
---

# Réinitialisez votre mot de passe

Que vous ayez oublié votre mot de passe ou que vous souhaitiez simplement en avoir un nouveau, cette page couvre les deux cas : récupérer l'accès depuis la page de connexion, et changer votre mot de passe délibérément depuis votre profil.

## Si vous avez oublié votre mot de passe

1. Sur la page de connexion, choisissez le lien **mot de passe oublié**.
2. Saisissez votre adresse email et validez.
3. Ouvrez l'email que KolleK vous envoie et suivez le lien de réinitialisation.
4. Choisissez un nouveau mot de passe et confirmez le. Vous pouvez désormais vous connecter avec.

Deux comportements méritent d'être connus pour ne pas vous surprendre.

- **Le message de confirmation est toujours le même**, qu'un compte existe ou non pour l'adresse saisie. Cela protège votre vie privée en ne révélant jamais qui est inscrit. Si vous avez un compte, l'email arrivera.
- **Le lien de réinitialisation expire après 60 minutes.** Si vous l'ouvrez trop tard, demandez en simplement un autre.

:::note
Si vous préférez éviter complètement la réinitialisation, un @doc(auth.magicLinks, "lien magique") peut vous connecter sans mot de passe. Une fois connecté, vous pouvez définir un nouveau mot de passe depuis votre profil.
:::

## Si vous voulez simplement le changer

Vous n'avez pas besoin du parcours mot de passe oublié pour renouveler votre mot de passe. Rendez vous sur votre profil, ouvrez la zone de sécurité, et changez votre mot de passe à cet endroit. Vous saisirez votre mot de passe actuel et choisirez le nouveau.

## Pourquoi un mot de passe peut être refusé

KolleK vérifie chaque nouveau mot de passe selon deux règles, afin qu'un refus ne soit jamais un mystère.

- **Au moins huit caractères.** Les mots de passe plus courts sont refusés d'office.
- **Aucun mot de passe compromis connu.** Votre mot de passe candidat est vérifié par rapport aux listes de mots de passe apparus dans des fuites de données publiques. S'il a déjà fuité quelque part, il est refusé, même s'il paraît robuste. Cela concerne le mot de passe lui même, pas votre compte, alors choisissez quelque chose que vous n'avez pas utilisé sur d'autres sites.

Un gestionnaire de mots de passe contourne les deux règles sans effort en générant quelque chose de long et unique.

## Et ensuite

- Ajoutez une seconde étape pour qu'un mot de passe volé ne suffise pas : @doc(security.twoFactorAuth).
- Vous n'arrivez toujours pas à entrer ? Suivez @doc(troubleshooting.signIn).
- L'email de réinitialisation n'est jamais arrivé ? Voir @doc(troubleshooting.emailDelivery).
