---
id: dataSafety.restoreFromTrash
title: Restaurer un élément depuis la corbeille
slug: restaurer-depuis-la-corbeille
section: protection-des-donnees
---

# Restaurer un élément depuis la corbeille

La plupart des suppressions courantes dans KolleK ne sont pas définitives. Les collections, les objets, les exemplaires, les catégories et les séries passent d'abord par la corbeille, où ils attendent avant d'être supprimés pour de bon. Cette page explique ce qui y atterrit, combien de temps ça y reste, et comment restaurer un élément.

Vous devez avoir le rôle éditeur ou propriétaire pour restaurer ou supprimer définitivement.

## Ce qui va à la corbeille, et ce qui n'y va pas

Cinq types d'objets sont supprimés en douceur vers la corbeille :

- @doc(collections.manage, "Les collections"), avec tout ce qu'elles contiennent
- @doc(items.addAndEdit, "Les objets")
- @doc(copies.track, "Les exemplaires")
- @doc(categories.organizeItems, "Les catégories")
- @doc(sets.trackCompletion, "Les séries")

:::note
Les photos, les documents, et l'historique d'un exemplaire (transactions, estimations, prêts, et le reste) ne vont pas à la corbeille. Supprimer l'un de ces éléments le retire immédiatement et définitivement.
:::

## Combien de temps les éléments sont conservés

Les objets mis à la corbeille sont conservés pendant une période de rétention, 30 jours sauf si la personne qui gère votre instance en a configuré une autre. Un nettoyage quotidien supprime définitivement tout ce qui a dépassé son délai. Chaque entrée de la corbeille indique combien de jours il lui reste, et la liste est triée en commençant par les plus urgentes, de sorte que ce qui est sur le point de disparaître apparaît en premier.

## Restaurer un élément

::::steps
:::step title="Ouvrez la corbeille"
Allez dans **Corbeille** depuis votre compte. Vous pouvez la parcourir par recherche si la liste est longue.

::screenshot{label="Liste de la corbeille avec le nombre de jours restants par entrée"}
:::

:::step title="Trouvez l'entrée"
Chaque entrée indique de quoi il s'agit, quand elle a été supprimée, et qui l'a supprimée.
:::

:::step title="Restaurez-la"
Choisissez **Restaurer**. L'objet revient exactement à sa place, avec ses données intactes.
:::
::::

Si vous avez supprimé une collection par erreur, la restaurer ramène aussi ce qu'elle contenait. Restaurez les éléments parents avant de chercher leurs enfants.

## Vider la corbeille

Vous pouvez aussi supprimer définitivement tout le contenu de la corbeille d'un coup, sans attendre la fin de la période de rétention.

:::warning
Vider la corbeille est définitif. Tout son contenu est supprimé pour de bon, et rien ne peut être récupéré ensuite.
:::

## Pour aller plus loin

- Vous voulez supprimer vous-même plutôt que vos données ? Consultez @doc(users.deleteSelf).
- Vous êtes en auto hébergement et voulez de vrais filets de sécurité ? Consultez @doc(selfHosting.backupAndRestore).
