---
id: users.deleteSelf
title: Supprimer votre utilisateur
slug: delete-your-user
section: data-safety
---

# Supprimer votre utilisateur

Supprimer votre utilisateur vous retire, vous, la personne, de KolleK. Ce n'est pas la même chose que supprimer le compte : le compte est l'espace de travail partagé, et @doc(accounts.delete, "le supprimer") détruit tout pour tout le monde. Cette page traite de la suppression de vous-même uniquement.

## Avant de décider

Deux situations ressemblent à « supprimer mon utilisateur » mais n'en sont pas :

- **Vous voulez que tout disparaisse.** Si vous êtes propriétaire et voulez que tout le catalogue et l'espace de travail soient supprimés, il s'agit de @doc(accounts.delete).
- **Vous voulez quitter un compte partagé.** Supprimer votre utilisateur vous retire et laisse le compte et son catalogue aux autres membres.

Si vous êtes le seul propriétaire du compte et que d'autres membres restent, promouvez d'abord quelqu'un d'autre au rang de propriétaire depuis @doc(collaboration.manageMembersAndRoles, "la gestion des membres"), afin que le compte ne se retrouve pas sans propriétaire.

## Vous supprimer

::::steps
:::step title="Ouvrez les paramètres de votre profil"
Allez dans les paramètres de votre profil et trouvez la zone de danger en bas de page.
:::

:::step title="Indiquez pourquoi vous partez"
Une raison est requise (quelques mots suffisent, au moins trois caractères). Elle est transmise à la personne qui gère l'instance et aide à améliorer KolleK.

::screenshot{label="Formulaire de suppression d'utilisateur avec le champ de raison"}
:::

:::step title="Confirmez"
Confirmez la suppression dans la boîte de dialogue. Vous êtes déconnecté immédiatement et votre connexion cesse de fonctionner.
:::
::::

:::warning
Supprimer votre utilisateur est définitif. Votre connexion disparaît et ne peut pas être restaurée depuis l'application. Votre adresse e-mail redevient disponible, vous pourriez donc créer un tout nouveau compte plus tard, mais il partirait vide.
:::

## Ce qu'il advient de vos traces

L'historique d'activité du compte conserve son intégrité : les entrées que vous avez créées gardent votre nom tel qu'il était à l'époque, de sorte que le journal d'audit du travail partagé ne se retrouve pas troué lorsque vous partez.

## Pour aller plus loin

- Vous préférez un nettoyage automatique ? Consultez @doc(users.inactiveDeletion).
- Retirer quelqu'un d'autre d'un compte partagé se fait dans @doc(collaboration.manageMembersAndRoles).
