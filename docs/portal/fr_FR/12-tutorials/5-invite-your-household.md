---
id: tutorials.inviteHousehold
title: "Tutoriel : inviter votre foyer ou club et définir les permissions"
slug: inviter-votre-foyer
section: tutoriels
---

# Tutoriel : inviter votre foyer ou club et définir les permissions

Un compte KolleK est un espace de travail partagé, et y faire entrer des personnes en toute sécurité est surtout une question de choisir le bon rôle pour chacune. Dans ce tutoriel, vous inviterez deux personnes avec des rôles différents, verrez ce que chacune peut et ne peut pas faire, partagerez une collection publiquement tout en gardant une autre privée, et ajusterez un rôle après coup.

Nous allons suivre Emma, qui catalogue des comics avec son partenaire Sam et aime montrer sa collection à son ami Leo. Sam aide à la saisie de données, il a donc besoin de pouvoir modifier. Leo se contente de parcourir, il ne devrait donc rien pouvoir changer.

Comptez environ quinze minutes pour ce tutoriel, plus le temps que vos invités mettront à ouvrir leur e-mail.

## Avant de commencer

- Vous devez être **propriétaire** du compte. Seuls les propriétaires peuvent inviter des personnes et changer les rôles.
- Lisez @doc(accounts.usersAndRoles) si ce n'est pas déjà fait. La version en une ligne : les lecteurs lisent, les éditeurs modifient le contenu du catalogue, les propriétaires gèrent en plus le compte.
- Connaissez les adresses e-mail de vos invités, et une chose à leur sujet : une invitation ne fonctionne que pour une adresse qui n'a pas déjà son propre compte KolleK, car une personne appartient à exactement un seul compte.

## Étape 1 : inviter Sam comme éditeur

::::steps
:::step title="Ouvrir la zone des membres"
Allez dans les paramètres des membres de votre compte, où les membres et les invitations en attente sont listés.

::screenshot{label="Écran des membres avec le formulaire d'invitation"}
:::

:::step title="Envoyer l'invitation"
Saisissez l'**e-mail** de Sam, choisissez le rôle **Éditeur**, et envoyez. Sam peut désormais créer et modifier des collections, des objets et des exemplaires, mais ne peut pas inviter des personnes ni toucher aux paramètres du compte.
:::
::::

L'e-mail d'invitation contient un lien valable **sept jours**. S'il expire avant que Sam ne l'ouvre, invitez-le simplement à nouveau.

## Étape 2 : inviter Leo comme lecteur

Répétez les mêmes étapes pour Leo, mais laissez le rôle sur **Lecteur**, qui est la valeur par défaut. Leo pourra parcourir tout dans le compte, y compris les collections, les objets et leurs historiques, mais chaque contrôle de modification restera hors de sa portée.

Choisir le rôle le plus restreint n'est pas inamical. Cela protège aussi Leo : il ne peut pas supprimer accidentellement un objet ou modifier un enregistrement en parcourant.

## Étape 3 : ce que vivent Sam et Leo

Chacun reçoit un e-mail et ouvre le lien. Comme ni l'un ni l'autre n'a encore de compte KolleK, la page leur demande de définir leur **prénom**, leur **nom**, et un **mot de passe** (au moins huit caractères, et vérifié contre les fuites connues). Ils atterrissent ensuite dans le compte d'Emma, déjà vérifiés et connectés, avec le rôle qu'elle a choisi.

Si le lien indique qu'un compte existe déjà pour cette adresse, cette personne ne peut pas rejoindre le compte via cette invitation. Cette situation et d'autres accrocs d'invitation sont couverts dans @doc(troubleshooting.signIn).

## Étape 4 : définir la visibilité de chaque collection

Les rôles contrôlent les personnes à l'intérieur du compte. La @doc(sharing.overview, "visibilité") enregistre à qui chaque collection est destinée, de vous seul à quiconque a un lien.

Emma a deux collections : « Mes Comics », qu'elle souhaite montrer au monde un jour, et « Recherche liste de souhaits », qui ne regarde qu'elle.

::::steps
:::step title="Marquer une collection comme publique"
Sur « Mes Comics », elle règle la visibilité sur **Publique**, la marquant comme celle qu'elle compte partager au-delà du compte.
:::

:::step title="Marquer l'autre comme privée"
« Recherche liste de souhaits » est réglée sur **Privée**, destinée à elle seule. **Partagée**, le réglage intermédiaire, marque une collection comme destinée à chaque membre du compte.
:::
::::

:::note
La visibilité n'est pas encore appliquée. Aujourd'hui, Sam et Leo peuvent toujours parcourir toutes les collections du compte, y compris les privées, et il n'existe aucun lien public à faire circuler, donc rien n'est visible en dehors du compte pour l'instant. Définir la visibilité maintenant signifie que chaque collection se comportera correctement dès que le partage arrivera. Voir @doc(troubleshooting.featureStatus).
:::

:::warning
Quand les liens publics arriveront, une collection publique sera consultable par quiconque possède le lien, sans se connecter. Ne marquez une collection comme publique que si vous êtes à l'aise avec le fait que chaque objet qu'elle contient soit vu.
:::

Le guide complet, y compris comment revenir en arrière, se trouve dans @doc(collections.share).

## Étape 5 : ajuster un rôle plus tard

Quelques semaines plus tard, Leo commence à repérer des erreurs et veut les corriger lui-même. Emma ouvre l'écran des membres, trouve Leo, et change son rôle de **Lecteur** à **Éditeur**. Le changement s'applique immédiatement. Les rôles sont un curseur, pas une sentence à vie, et rétrograder quelqu'un fonctionne de la même façon.

Une protection à connaître : un compte doit toujours garder au moins un **propriétaire**. KolleK refusera de rétrograder ou de retirer le dernier propriétaire, de sorte qu'un compte partagé ne peut jamais se retrouver sans propriétaire et ingérable.

:::warning
Retirer un membre supprime son utilisateur du compte et ne peut pas être annulé depuis l'écran des membres. Si quelqu'un a simplement besoin de moins d'accès, changez son rôle plutôt que de le retirer.
:::

## Le résultat

Le compte d'Emma compte désormais trois personnes avec trois niveaux de confiance : Emma possède et gère, Sam catalogue à ses côtés, et Leo parcourt et, dernièrement, met de l'ordre. Une collection est marquée pour le monde, une pour elle seule, prête pour le jour où le partage sera appliqué. Rien dans cette configuration n'est figé, les rôles et la visibilité peuvent changer à mesure que les personnes changent.

## Erreurs courantes à éviter

- **Inviter tout le monde comme éditeur par défaut.** Donnez le rôle dont la personne a besoin aujourd'hui. La faire passer à un niveau supérieur plus tard ne prend qu'un clic.
- **Supposer que privé cache déjà une collection.** La visibilité est enregistrée mais pas encore appliquée, donc chaque membre peut parcourir chaque collection aujourd'hui, privée ou non. Gardez les catalogues vraiment personnels dans votre propre compte pour l'instant.
- **Retirer un membre pour réduire son accès.** Le retrait est destructif. Les changements de rôle ne le sont pas.

## Et ensuite

- La référence complète de qui peut faire quoi se trouve dans @doc(collaboration.rolesInPractice, "Comprendre les trois rôles en pratique").
- Gérez le compte lui-même, nom, devise, et plus, dans @doc(accounts.settings).
- Vous hébergez vous-même l'instance pour votre club ? Voir @doc(tutorials.selfHostWithDocker, "Auto-héberger KolleK avec Docker").
