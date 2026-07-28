---
id: account.freePlan
title: Le forfait gratuit et le déverrouillage de votre compte
slug: forfait-gratuit-et-deverrouillage
section: compte-et-profil
---

# Le forfait gratuit et le déverrouillage de votre compte

Si vous faites tourner KolleK vous même, cette page ne vous concerne pas. Une instance auto hébergée n'a aucune limite d'objets, aucun écran de mise à niveau et rien à acheter. Passez plutôt à @doc(selfHosting.index).

Sur une instance hébergée, celle que nous faisons tourner pour les personnes qui préfèrent ne pas gérer de serveur, un compte démarre sur un forfait gratuit. Cette page explique ce que ce forfait contient, ce qui se passe quand vous le remplissez, et ce qu'implique le déverrouillage.

## Ce que contient le forfait gratuit

Un compte gratuit contient **dix objets**, comptés sur l'ensemble de vos collections. Deux collections de cinq objets remplissent le forfait aussi sûrement qu'une collection de dix.

Au delà de ces dix, KolleK en accepte **cinq de plus** par tolérance. Rien ne change dans le compte à ce moment là, si ce n'est le ton des écrans : ils vous disent clairement que vous avez dépassé le forfait, et tout continue de fonctionner.

À **quinze objets**, le compte cesse de grandir. Vous ne pouvez pas en ajouter un seizième tant que le compte n'est pas déverrouillé.

Seuls les objets sont comptés. Les collections, les catégories, les étiquettes, les emplacements, les séries, les ensembles, les photos, les documents et les personnes que vous invitez ne le sont pas, et vous pouvez continuer à en créer. Ajouter un exemplaire à un objet que vous avez déjà ne compte pas non plus, puisqu'un exemplaire appartient à un objet plutôt que d'exister seul. Voir @doc(items.itemsVsCopies) si cette distinction est nouvelle pour vous.

:::note
Rien de ce que vous avez déjà catalogué n'est jamais supprimé, masqué ni passé en lecture seule. Chaque objet, photo, estimation et prêt reste exactement où il est, et reste cherchable et exportable. La seule chose qui s'arrête, c'est l'ajout de nouveautés.
:::

## Ce que vous verrez en le remplissant

**La carte d'utilisation dans la barre latérale.** Dès votre premier objet, le bas de la barre latérale montre la part du forfait utilisée, sous forme de barre et de compteur. En dessous de dix, elle vous dit simplement combien d'objets il reste. Au delà de dix, elle passe en avertissement et vous dit de combien vous dépassez.

**Une bannière sur l'écran de collection.** Une fois les dix objets dépassés, ouvrir une collection affiche une bannière expliquant où vous en êtes. Dans la tolérance, elle vous dit combien d'objets vous pouvez encore ajouter. À quinze, elle vous dit que l'ajout est en pause.

**Le bouton Ajouter un objet cesse de fonctionner.** À quinze objets, le bouton **Ajouter un objet** d'une collection est désactivé plutôt que masqué, pour qu'il soit clair que le bouton existe toujours et que seule la réserve est épuisée. Tout le reste de l'écran continue de fonctionner : vous pouvez encore modifier des objets, ajouter des exemplaires, téléverser des photos et enregistrer des prêts.

## Déverrouiller le compte

La bannière et la carte d'utilisation mènent toutes deux à l'écran **Forfait et facturation**, qui présente les deux voies honnêtes qui s'offrent à vous.

La première consiste à déverrouiller le compte par un paiement unique. Il supprime la limite d'objets pour de bon. Il n'y a pas d'abonnement, pas de renouvellement et pas de seconde facture, et tout ce que vous avez déjà catalogué est repris intact.

La seconde consiste à passer à l'auto hébergement, qui est gratuit, fait tourner le même code et n'a aucune limite d'objets. Nous mettons les deux sur le même écran délibérément. Voir @doc(kollek.hostingOptions) pour les compromis entre les deux.

:::note
Toute personne du compte peut consulter l'écran **Forfait et facturation**, parce que n'importe qui peut se heurter à la limite. Seul un propriétaire peut aller plus loin et déverrouiller le compte. Si vous êtes éditeur ou lecteur, demandez à un propriétaire de prendre le relais. Voir @doc(accounts.usersAndRoles).
:::

## L'étape de confirmation

Choisir de déverrouiller amène un propriétaire à un écran de confirmation, avant tout paiement. Il existe parce que le paiement est **définitif et non remboursable**, et que nous préférons le dire sur un écran que vous devez lire plutôt que dans des conditions que vous ne lirez pas.

L'écran vous demande de cocher quatre cases, chacune correspondant à un point distinct à accepter :

- qu'il s'agit d'un paiement unique et qu'il n'est pas remboursable
- que vous écrirez au support plutôt que d'engager une rétrofacturation auprès de votre banque en cas de problème
- que vous savez que l'auto hébergement est gratuit et que vous choisissez délibérément le compte hébergé
- que vous avez regardé ce que comprend le déverrouillage et que cela couvre vos besoins

Les quatre doivent être cochées avant de pouvoir continuer. Ce que vous avez confirmé est enregistré, ainsi que la personne qui l'a confirmé, la date et l'adresse d'où venait la confirmation. La personne qui exploite l'instance peut voir cet enregistrement sur le compte. Il est conservé comme preuve de ce qui a été accepté, et n'est utilisé à rien d'autre.

:::warning
Une fois le paiement possible et effectué, il n'y a pas de remboursement. Décidez avant de payer, pas après. En cas de doute, le forfait gratuit et l'auto hébergement vous laissent tous deux le temps de vous décider sans rien dépenser.
:::

## Le paiement n'est pas encore ouvert

La limite est appliquée dès aujourd'hui, et les deux écrans ci dessus sont terminés, y compris l'étape de confirmation et son enregistrement. Le prestataire de paiement derrière eux n'est pas encore branché : il n'existe donc pour l'instant aucun moyen de payer réellement ni de déverrouiller un compte.

Si vous atteignez la limite avant l'ouverture du paiement, vos options sont de supprimer des objets que vous ne souhaitez plus suivre, ou de passer à une instance auto hébergée, gratuite et illimitée. Voir @doc(troubleshooting.featureStatus) pour l'état actuel de ce point et de tout ce qui est encore en chemin.

## Et ensuite

- Vous pesez les deux façons de faire tourner KolleK ? Lisez @doc(kollek.hostingOptions).
- Prêt à le faire tourner vous même ? Commencez par @doc(selfHosting.installDocker).
- Vous vous demandez ce qui ne compte pas du tout ? Voir @doc(accounts.settings).
