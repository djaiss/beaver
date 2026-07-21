---
id: copies.track
title: Suivre les exemplaires que vous possédez
slug: track-the-copies-you-own
section: core-features
---

# Suivre les exemplaires que vous possédez

Un objet à lui seul n'est qu'une description. Un **exemplaire** est votre enregistrement d'un exemplaire physique que vous possédez réellement, avec son propre état, emplacement, statut et historique. Cette page couvre l'ajout d'exemplaires et chaque champ d'un exemplaire.

L'idée derrière cette séparation est expliquée dans @doc(items.itemsVsCopies). Ajouter des exemplaires nécessite le rôle **éditeur** ou **propriétaire**.

## Ajouter un exemplaire

Les exemplaires s'ajoutent sur le formulaire de l'objet, directement sur place, afin que vous puissiez les enregistrer pendant le catalogage.

::::steps
:::step title="Ouvrir l'objet"
Ouvrez l'objet et choisissez de le modifier, puis ajoutez un **exemplaire**.
:::

:::step title="Enregistrez son état physique"
Choisissez son **état** dans la liste et sélectionnez l'**emplacement** où il est stocké.

::screenshot{label="Ligne d'exemplaire, champs état et emplacement"}
:::

:::step title="Définissez son statut et ses détails"
Laissez le **statut** sur Possédé pour quelque chose que vous avez, ou choisissez un autre statut. Remplissez les autres champs applicables, puis enregistrez l'objet.
:::
::::

Vous en possédez deux du même ? Ajoutez un second exemplaire au même objet, jamais un second objet. Chaque exemplaire garde son propre état, emplacement et historique.

## Les champs de l'exemplaire

- **Identifiant.** Un numéro de série, un numéro de coque, ou toute marque qui identifie précisément cet exemplaire. Priya enregistre le numéro de série gravé sur chacune de ses montres.
- **@doc(conditions.overview, "État").** La qualité de cet exemplaire, choisie dans la liste prête à l'emploi (Neuf, Comme neuf, Usagé, Usé, Endommagé, plus tout ce que votre compte a ajouté).
- **@doc(locations.overview, "Emplacement").** Où vit actuellement l'exemplaire. Le changer plus tard via un déplacement conserve l'historique ; voir @doc(copies.move, "Déplacer un exemplaire").
- **Statut.** Où en est l'exemplaire dans son cycle de vie. Voir la liste ci dessous.
- **Quantité.** Pour des exemplaires identiques et interchangeables que vous n'avez pas besoin de distinguer, comme dix exemplaires du même paquet de boosters non ouvert. Si chaque exemplaire compte individuellement, donnez plutôt à chacun sa propre ligne.
- **Date de cession.** Quand l'exemplaire a quitté vos mains, pour des statuts comme Vendu ou Cédé.
- **Note.** Tout ce qui vaut la peine d'être retenu à propos de cet exemplaire en particulier.
- **Valeur estimée.** Un chiffre rapide pour ce que vaut l'exemplaire. En coulisses, il est enregistré comme une @doc(copies.recordPaymentsAndValue, "valorisation") de type « Estimation personnelle », ouvrant l'historique de valeur de l'exemplaire plutôt que de rester posé sur l'exemplaire lui même. Pour tout ce qui compte pour vous, ajoutez y de vraies valorisations datées.

## Le cycle de vie du statut

- **Possédé.** En votre possession. La valeur par défaut.
- **Commandé.** Acheté mais pas encore arrivé.
- **Prêté.** Chez quelqu'un d'autre, mais toujours à vous. La garde a changé, pas la propriété, donc l'exemplaire compte toujours comme détenu. Les prêts sont mieux enregistrés via @doc(loans.lendAndBorrow), qui définit ce statut pour vous.
- **Vendu, Offert.** La propriété est passée à quelqu'un d'autre.
- **Perdu, Volé.** Disparu sans votre consentement.
- **Cédé.** Jeté ou recyclé.
- **Autre.** Tout ce que la liste ne couvre pas.

Possédé, Commandé et Prêté comptent comme « toujours détenu ». Les autres enregistrent des exemplaires qui ont quitté la collection mais dont vous voulez conserver l'historique.

## Où vit l'argent

Vous remarquerez peut être qu'il n'y a pas de champ « prix payé » sur l'exemplaire. C'est délibéré. Ce que vous avez payé, et quand vous avez acquis l'exemplaire, proviennent de ses **transactions**, et ce qu'il vaut au fil du temps provient de ses **valorisations**. Cela conserve l'histoire complète de l'argent au lieu d'un simple chiffre écrasé à chaque fois. Commencez par @doc(copies.recordPaymentsAndValue).

## Et ensuite

- Comprenez les enregistrements que peut porter un exemplaire : @doc(copyHistory.concept, "L'historique d'un exemplaire expliqué").
- Enregistrez l'achat : @doc(copies.recordPaymentsAndValue).
- Gardez son adresse à jour : @doc(copies.move).
