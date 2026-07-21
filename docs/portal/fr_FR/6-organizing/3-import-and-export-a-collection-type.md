---
id: collectionTypes.importExport
title: Importer et exporter un type de collection
slug: import-and-export-a-collection-type
section: organizing
---

# Importer et exporter un type de collection

Un @doc(collectionTypes.overview, "type de collection") soigneusement construit mérite d'être partagé. KolleK peut exporter une définition de type sous forme de fichier JSON et en importer une, afin que vous puissiez copier une configuration entre comptes, la partager avec un autre collectionneur, ou en garder un instantané avant de la retravailler.

Vous avez besoin du rôle éditeur ou propriétaire.

## Ce qui se déplace, et ce qui ne se déplace pas

L'export contient uniquement la définition du type : son nom, sa couleur, ses groupes de champs, ses champs personnalisés, et les options de tout champ de type liste.

:::note
Exporter un type n'exporte pas les objets ni leurs données. Il n'existe actuellement aucun import ou export d'objet ou de collection entière. Consultez la @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités") pour savoir où cela en est, et @doc(dataSafety.backupCollectionData) pour connaître la portabilité qui existe aujourd'hui.
:::

## Exporter un type

::::steps
:::step title="Ouvrir le type"
Dans les paramètres du compte, ouvrez **Types de collection** et sélectionnez le type que vous voulez exporter.
:::

:::step title="L'exporter"
Choisissez **Exporter**. KolleK télécharge un fichier JSON décrivant le type.

::screenshot{label="Éditeur de type avec l'option d'export"}
:::
::::

Le fichier est du texte brut. Vous pouvez le lire, le garder avec vos sauvegardes, ou l'envoyer à quelqu'un.

## Importer un type

L'import fonctionne à partir de JSON collé, alors ouvrez d'abord le fichier reçu dans n'importe quel éditeur de texte et copiez son contenu.

::::steps
:::step title="Démarrer l'import"
Dans les paramètres du compte, ouvrez **Types de collection** et choisissez **Importer**.
:::

:::step title="Coller le JSON"
Collez la définition du type dans le champ et confirmez. KolleK la valide et crée le type avec ses groupes, ses champs et ses options.

::screenshot{label="Formulaire d'import avec du JSON collé"}
:::

:::step title="Vérifier le résultat"
Ouvrez le nouveau type et vérifiez que les champs sont bien arrivés, puis rattachez le à une collection pour commencer à l'utiliser.
:::
::::

## Un exemple concret

L'ami de Noah collectionne lui aussi des vinyles et a peaufiné un type "Disques vinyles" avec un ensemble de champs groupés : informations de sortie (artiste, album, année de sortie) et détails de pressage (pressage, vitesse, vinyle coloré). Plutôt que de le reconstruire à la main, Noah demande l'export, colle le JSON dans son propre compte, et obtient la structure identique en quelques secondes.

Si vous voulez voir le format exact attendu par l'importeur, exportez d'abord n'importe quel type existant, comme le type Bandes dessinées prêt à l'emploi, et utilisez le comme modèle. Vos propres exports s'importent toujours proprement.

## Où aller ensuite

- Affinez le type importé dans @doc(collectionTypes.setup).
- Comprenez ce qui peut et ne peut pas être exporté dans @doc(dataSafety.backupCollectionData).
