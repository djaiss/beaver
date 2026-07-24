---
id: search.overview
title: Chercher dans tout votre compte
slug: chercher-dans-tout-votre-compte
section: fonctionnalites
---

# Chercher dans tout votre compte

Emma collectionne des bandes dessinées, des vinyles et des cartes à collectionner. Elle a trois collections, quelques centaines d'objets et deux fois plus d'exemplaires. Quelqu'un lui demande si elle possède toujours ce numéro de Spider-Man à la couverture noire. Elle ne devrait pas avoir à se rappeler dans quelle collection il se trouve.

La recherche répond à cette question depuis une seule zone de saisie. Tapez un mot, et KolleK regarde d'un coup dans tout ce que contient votre compte : objets, collections, exemplaires, photos, prêts, lieux, séries à compléter, sagas, catégories, étiquettes et documents.

Ouvrez-la depuis **Recherche**, en haut de la barre latérale, ou appuyez sur **⌘K** (**Ctrl K** sous Windows et Linux) depuis n'importe quel écran.

## Ce qui est pris en compte

La recherche ne regarde pas seulement les noms. Elle regarde aussi les mots classés autour d'une fiche, et c'est ce qui la rend utile plutôt que simplement littérale.

Chercher `spider` trouve :

- un objet nommé **Amazing Spider-Man #300**
- l'exemplaire **ASM-300-B**, parce que son objet porte ce nom
- une photo nommée `spider-man-300-front.jpg`
- un objet que vous n'avez jamais appelé « Spider-Man », parce que vous lui avez mis l'étiquette **spider-man**
- un prêt à un ami, parce que l'exemplaire prêté est cette bande dessinée

Une recherche qui part d'une étiquette, d'une catégorie ou d'un lieu vous amène donc quand même à ce que vous cherchiez vraiment.

:::note
Tout ce qui se trouve à la corbeille est laissé de côté. @doc(dataSafety.restoreFromTrash, "Restaurez-le") et il redevient trouvable.
:::

## Comment fonctionne la correspondance

Quatre règles suffisent.

**Chaque mot doit correspondre.** Ajouter un mot resserre le résultat au lieu de l'élargir. `miles davis` ne trouve que les fiches où les deux mots apparaissent quelque part. Si vous obtenez trop de résultats, ajoutez un mot.

**Un mot correspond depuis son début.** Taper `spi` trouve **Spider-Man**. Vous n'avez jamais besoin de finir un mot, mais vous devez le commencer : chercher `man` ne trouvera pas **Spider-Man**, parce que `man` n'est le début d'aucun de ses mots.

**La casse et la ponctuation sont ignorées.** `asm-300`, `asm 300` et `ASM_300` se comportent tous de la même façon, ce qui compte quand vos propres identifiants utilisent des tirets, des points ou des soulignés et que vous ne savez plus lequel.

**Les lettres seules sont ignorées.** Une lettre isolée est trop courante pour être indexée, elle est donc retirée de votre recherche. Si vous cherchez une seule lettre et rien d'autre, vous n'obtenez rien plutôt que tout.

## Lire les résultats

Les résultats sont regroupés par nature, les objets d'abord. Chaque ligne montre le nom, une pastille indiquant de quel type de fiche il s'agit, la collection à laquelle elle appartient le cas échéant, et une ligne de contexte : combien d'exemplaires a un objet, où un exemplaire est rangé, à qui est parti un prêt.

À droite de chaque ligne, **Correspondance sur le nom** signifie que tous les mots tapés se trouvaient dans le nom de la fiche elle-même. **Correspondance dans le texte** signifie qu'au moins un mot a été trouvé plus loin, par exemple dans une description ou une étiquette. Les correspondances sur le nom sont classées en premier, donc la réponse la plus proche est en général la première ligne.

Les pastilles au-dessus des résultats permettent de se limiter à un seul type de fiche. Chacune a sa propre adresse : une recherche limitée aux objets peut donc être mise en favori ou partagée avec un collègue du même compte.

Au plus 50 résultats sont affichés. Quand il y en a davantage, le compte sous la liste indique combien ont correspondu au total, et ajouter un mot à votre recherche est le moyen le plus rapide d'atteindre celui que vous voulez.

## Qui peut chercher quoi

La recherche est limitée à votre compte. Vous ne voyez jamais rien qui appartienne à un autre compte, et personne en dehors du vôtre ne voit le vôtre.

À l'intérieur de votre compte, tous les rôles peuvent chercher, et chaque résultat ouvre un écran que ce rôle a le droit d'ouvrir. Les @doc(accounts.usersAndRoles, "lecteurs") font exception sur un point : les étiquettes se gèrent sur un écran réservé aux propriétaires et aux éditeurs, un lecteur n'obtient donc pas de résultats d'étiquettes à part entière. Il trouve tout de même tout ce qui *porte* l'étiquette `spider-man`, parce que les noms d'étiquettes comptent pour les objets qui les portent.

## S'il manque quelque chose

La recherche lit un index tenu à jour au fil de votre travail, donc un nouvel objet est trouvable dès que vous l'enregistrez.

Deux cas peuvent le laisser brièvement en retard :

**Vous avez renommé quelque chose que d'autres fiches mentionnent.** Renommer une collection oblige à réindexer chacun de ses objets sous le nouveau nom. Cela se fait en arrière-plan : laissez-lui un instant.

**Vous venez de mettre à jour une instance auto-hébergée.** L'index part vide sur une installation existante et doit être construit une fois. Lancez @doc(selfHosting.cliCommands, "la commande de reconstruction") et tout devient trouvable :

```bash
php artisan search:rebuild-index
```

Cette commande peut être relancée à tout moment sans risque, c'est donc aussi le remède si l'index dérive un jour.

## Et ensuite

- @doc(items.tagAndFind) couvre l'étiquetage, ce qui permet à la recherche de trouver des choses que vous n'avez pas nommées littéralement.
- @doc(organizing.categoriesSetsAndSeries) explique les catégories, séries à compléter et sagas qui alimentent aussi la recherche.
- @doc(photos.library) dispose de sa propre recherche, pour quand vous parcourez des photos plutôt que tout le compte.
