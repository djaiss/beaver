---
id: collaboration.manageMembersAndRoles
title: Gérer les membres et les rôles
slug: gerer-membres-et-roles
section: collaboration
---

# Gérer les membres et les rôles

L'implication des personnes évolue avec le temps. Un lecteur commence à aider à la saisie de données et doit devenir éditeur. Quelqu'un quitte le club et doit perdre son accès. Cette page couvre le changement du rôle d'un membre et sa suppression, ainsi que la seule protection qui empêche votre compte de se retrouver bloqué.

Vous devez être **@doc(accounts.usersAndRoles, "propriétaire")** pour tout ce qui figure sur cette page. La liste des membres et les invitations en attente ne sont visibles que par les propriétaires.

## Voir qui fait partie de votre compte

Ouvrez la zone des membres depuis les paramètres de votre compte. Vous y verrez :

- Les **membres**, chacun avec son nom, son e-mail, et son rôle actuel.
- Les **invitations en attente**, envoyées mais pas encore acceptées, pour savoir qui est encore en train de rejoindre le compte. Les invitations expirent au bout de sept jours.

## Changer le rôle d'un membre

::::steps
:::step title="Trouvez le membre"
Dans la liste des membres, repérez la personne dont vous voulez changer l'accès.
:::

:::step title="Choisissez le nouveau rôle"
Changez son **rôle** en lecteur, éditeur, ou propriétaire. Le changement prend effet immédiatement, il n'y a ni e-mail de confirmation ni étape d'acceptation.

::screenshot{label="Ligne d'un membre avec le sélecteur de rôle ouvert"}
:::
::::

Quand le rôle de Sam passe de lecteur à éditeur, Sam peut commencer à ajouter et modifier des objets dès que le changement est enregistré.

:::note
Un compte doit toujours conserver au moins un propriétaire. KolleK refusera de rétrograder le dernier propriétaire, afin que vous ne puissiez pas laisser accidentellement le compte sans personne pour le gérer. Promouvez d'abord quelqu'un d'autre au rang de propriétaire si vous souhaitez vous retirer.
:::

## Retirer un membre

Retirer un membre lui enlève entièrement son accès.

:::warning
Retirer un membre supprime son utilisateur. Il perd immédiatement son accès, et cette action ne peut pas être annulée depuis cet écran. S'il doit revenir plus tard, vous devrez l'inviter à nouveau et il repartira de zéro.
:::

Ses contributions passées ne disparaissent pas pour autant. @doc(activity.feedAndAuditTrail, "Le journal d'activité") conserve la trace de ce qu'il a fait, car chaque entrée enregistre le nom de la personne au moment où elle a été écrite.

La même protection s'applique ici que pour les rôles : le dernier propriétaire ne peut pas être retiré.

## Et ensuite

- Comparez ce que permet chaque rôle dans @doc(collaboration.rolesInPractice).
- Faites entrer quelqu'un de nouveau avec @doc(collaboration.invitePeople).
- Si vous souhaitez plutôt fermer complètement le compte, lisez @doc(accounts.delete).
