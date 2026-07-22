---
id: users.inactiveDeletion
title: Supprimer automatiquement un utilisateur inactif
slug: suppression-dun-utilisateur-inactif
section: protection-des-donnees
---

# Supprimer automatiquement un utilisateur inactif

KolleK propose un paramètre optionnel qui supprime automatiquement votre utilisateur après une longue période d'inactivité, environ six mois. Il existe pour les personnes qui préfèrent ne pas laisser traîner une connexion dormante sur un serveur où elles ne reviendront peut être jamais.

## Comment ça marche

- Le paramètre est **désactivé tant que vous ne l'activez pas**, et il ne s'applique qu'à vous. Personne ne peut l'activer à votre place, et votre choix n'affecte aucun autre membre du compte.
- Une fois activé, une vérification quotidienne regarde la date de votre dernière activité. Si plus d'environ six mois se sont écoulés, votre utilisateur est supprimé, comme si vous l'aviez @doc(users.deleteSelf, "supprimé vous-même").
- Toute activité réinitialise le compteur. Se connecter suffit.

## Activer ou désactiver

Trouvez l'option dans les paramètres de sécurité de votre profil, choisissez oui ou non, et enregistrez. Vous pouvez changer d'avis à tout moment avant que la suppression n'ait lieu.

:::warning
Si la suppression pour inactivité se déclenche, votre utilisateur est supprimé définitivement et votre connexion cesse de fonctionner, sans e-mail d'avertissement supplémentaire. N'activez ceci que si vous souhaitez réellement qu'une connexion dormante disparaisse d'elle-même.
:::

## Est-ce fait pour vous

Activez-le si vous êtes en train d'essayer KolleK, ou suffisamment soucieux de votre vie privée pour qu'une connexion abandonnée vous dérange. Laissez-le désactivé si ce catalogue est une archive de long terme que vous pourriez ne pas toucher pendant un an et que vous voulez quand même retrouver. Les collections sont des choses patientes ; un écart de huit mois entre deux sessions de catalogage est normal dans ce loisir.

## Pour aller plus loin

- Supprimez votre utilisateur délibérément à la place dans @doc(users.deleteSelf).
- Consultez votre propre activité récente dans @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et les e-mails envoyés").
