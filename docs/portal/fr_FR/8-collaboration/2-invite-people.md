---
id: collaboration.invitePeople
title: Inviter des personnes dans votre compte
slug: invite-people
section: collaboration
---

# Inviter des personnes dans votre compte

Cataloguer est plus amusant, et plus précis, quand les personnes qui partagent la collection partagent aussi le catalogue. Cette page vous montre comment inviter quelqu'un dans votre compte, ce que cette personne va vivre, et les limites à connaître avant d'envoyer l'invitation.

Seul un **@doc(accounts.usersAndRoles, "propriétaire")** peut inviter des personnes. Si vous ne voyez pas cette option, demandez à un propriétaire de votre compte.

## Décidez d'abord du rôle

Chaque invitation porte un @doc(collaboration.rolesInPractice, "rôle"), choisi au moment de l'invitation.

- **Lecteur** peut tout parcourir mais ne rien changer. C'est le rôle par défaut.
- **Éditeur** peut créer et modifier le contenu du catalogue.
- **Propriétaire** peut tout faire, y compris gérer les membres et les paramètres du compte.

Faites démarrer les gens avec le rôle le plus bas qui convienne. Vous pourrez toujours @doc(collaboration.manageMembersAndRoles, "l'augmenter plus tard"), ce qui est plus simple que de retirer un accès que quelqu'un n'aurait pas dû avoir.

Emma, par exemple, invite son partenaire Sam en tant qu'**éditeur** pour que Sam puisse aussi ajouter des bandes dessinées, et son amie Léo en tant que **lectrice** pour qu'elle puisse parcourir la collection sans pouvoir la modifier.

## Envoyer une invitation

::::steps
:::step title="Ouvrez les membres de votre compte"
Allez dans les paramètres de votre compte et ouvrez la zone des membres. Vous verrez les membres actuels et les invitations en attente.

::screenshot{label="Écran des membres avec le formulaire d'invitation"}
:::

:::step title="Saisissez l'adresse e-mail et choisissez un rôle"
Saisissez l'**adresse e-mail** de la personne et choisissez son **rôle**. Si vous laissez le rôle par défaut, elle rejoindra le compte en tant que lectrice.
:::

:::step title="Envoyez l'invitation"
Validez le formulaire. KolleK envoie à la personne un e-mail avec un lien pour rejoindre votre compte, et l'invitation apparaît dans votre liste des invitations en attente.
:::
::::

Si vous invitez à nouveau la même adresse e-mail alors qu'une invitation précédente est encore en attente et non expirée, KolleK réutilise l'invitation existante plutôt que d'en créer un doublon.

## Ce que vit la personne invitée

La personne reçoit un e-mail contenant un lien. En l'ouvrant, elle voit qui l'a invitée et pour quel compte. Pour rejoindre le compte, elle renseigne son **prénom**, son **nom**, et un **mot de passe**. Les mêmes garde fous s'appliquent qu'à l'inscription : au moins huit caractères, et rien qui soit déjà apparu dans une fuite de données connue.

Une fois le formulaire validé, elle devient membre de votre compte avec le rôle que vous avez choisi, son e-mail est déjà vérifié, et elle est connectée. Vous n'avez rien d'autre à faire.

## Les limites à connaître

:::note
Les invitations expirent au bout de sept jours. Si quelqu'un rate le délai, invitez le simplement à nouveau.
:::

Une limite mérite une attention particulière, car c'est la raison la plus fréquente d'échec d'une invitation.

- **Une personne appartient à exactement un seul compte.** Si l'e-mail que vous invitez possède déjà son propre compte KolleK, cette personne ne peut pas accepter votre invitation. Elle devra utiliser une autre adresse e-mail, ou @doc(users.deleteSelf, "supprimer son utilisateur existant") au préalable.
- **Seuls les propriétaires peuvent inviter.** Les éditeurs et les lecteurs ne peuvent pas faire entrer de nouvelles personnes.

Si un e-mail d'invitation n'arrive jamais, il se peut que la livraison des e-mails de l'instance ne soit pas encore configurée. Consultez @doc(troubleshooting.emailDelivery, "le dépannage de la livraison des e-mails").

## Et ensuite

- Ajustez les accès ou retirez quelqu'un dans @doc(collaboration.manageMembersAndRoles).
- Vérifiez précisément ce que permet chaque rôle dans @doc(collaboration.rolesInPractice).
- Suivez une mise en place complète dans le tutoriel @doc(tutorials.inviteHousehold, "Inviter votre foyer ou votre club").
