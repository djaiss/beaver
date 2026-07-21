---
id: collectionTypes.setup
title: Configurer les types de collection et les champs personnalisés
slug: set-up-collection-types-and-custom-fields
section: organizing
---

# Configurer les types de collection et les champs personnalisés

Un @doc(collectionTypes.overview, "type de collection") détermine quels détails un objet peut enregistrer. Une bande dessinée veut un numéro d'épisode et un éditeur. Un disque vinyle veut un artiste et un pressage. Cette page vous montre comment créer un type, lui ajouter des champs personnalisés, et garder des formulaires longs lisibles grâce aux groupes de champs.

Vous avez besoin du rôle éditeur ou propriétaire pour gérer les types. Les types sont partagés à l'échelle du compte, donc un type que vous configurez soigneusement une fois peut être réutilisé par n'importe quel nombre de collections.

## Partir des types prêts à l'emploi

Un compte tout juste créé inclut déjà une douzaine de types prêts à l'emploi (Bandes dessinées, Cartes à collectionner, Disques vinyles, CD, DVD, Pièces de monnaie, Timbres, Livres, Figurines / Jouets, Jeux vidéo, Montres et Vin), chacun avec des champs sensés déjà groupés. Avant de construire à partir de zéro, ouvrez celui qui se rapproche le plus de votre passion et ajustez le. Renommer un champ ou en ajouter un est plus rapide que de partir de rien.

## Créer un type

Noah collectionne des vinyles et veut un type pour les affiches de concert, quelque chose que les types par défaut ne couvrent pas.

::::steps
:::step title="Ouvrir les types de collection"
Allez dans les paramètres de votre compte et ouvrez **Types de collection**.

::screenshot{label="Liste des types de collection dans les paramètres du compte"}
:::

:::step title="Créer le type"
Choisissez **Nouveau type**, donnez lui un nom (Noah tape "Affiches de concert") et choisissez une couleur. La couleur vous aide à distinguer les types d'un coup d'œil dans les listes.
:::

:::step title="Ajouter vos premiers champs"
Ouvrez le nouveau type et ajoutez un champ personnalisé pour chaque détail que vous voulez enregistrer. Pour chaque champ, choisissez un nom et un type de champ.

::screenshot{label="Éditeur de type avec la liste des champs"}
:::
::::

L'éditeur de type enregistre au fur et à mesure. Il n'y a pas de bouton d'enregistrement séparé à retenir, chaque modification est stockée dès qu'elle est faite.

## Choisir le bon type de champ

Chaque champ personnalisé possède un des six types de champ suivants.

- **Texte** pour les détails libres, comme un artiste ou une salle de concert.
- **Nombre** pour les quantités et les mesures, comme un numéro d'épisode ou un tirage.
- **Date** pour tout ce qui est calendaire, comme une date de concert.
- **Oui / Non** pour des indicateurs simples, comme "Dédicacé" ou "Première édition".
- **Liste** pour un ensemble fixe d'options que vous définissez, comme un éditeur ou une note. Les options gardent les données cohérentes, parce que tout le monde choisit dans la même liste au lieu de taper des variantes.
- **Note** pour un score personnel d'une à cinq étoiles.

Préférez **Liste** à **Texte** dès que les valeurs possibles forment une liste connue. "Marvel" et "marvel comics" se ressemblent pour vous, mais pas pour un filtre.

## Garder les formulaires lisibles avec les groupes de champs

Les champs peuvent être organisés en groupes nommés, et chaque groupe s'affiche comme sa propre section sur le formulaire d'objet. Le type Bandes dessinées prêt à l'emploi, par exemple, regroupe ses champs en "Informations d'édition" et "État & évaluation". Les champs non groupés apparaissent en premier.

Créez un groupe, donnez lui un nom, et déplacez des champs dedans. Vous pouvez réordonner à la fois les champs à l'intérieur d'un groupe et les groupes eux mêmes, de sorte que le formulaire se lise dans l'ordre qui a du sens pour votre passion.

:::note
Les groupes n'affectent que la présentation du formulaire d'objet. Ils ne changent rien aux données elles mêmes, alors n'hésitez pas à réorganiser à tout moment.
:::

## Rattacher le type à des collections

Un type ne fait rien tant qu'une @doc(collections.overview, "collection") ne l'active pas. Quand vous créez ou modifiez une collection, choisissez quels types s'appliquent. Une collection peut en activer plusieurs, et le même type peut servir plusieurs collections. Une fois activé, les objets de cette collection peuvent choisir le type et remplir ses champs.

## Où aller ensuite

- Partagez une configuration dont vous êtes fier, ou empruntez en une, avec @doc(collectionTypes.importExport).
- Mettez les champs au travail dans @doc(items.addAndEdit).
- Complétez votre configuration avec @doc(locations.setup).
