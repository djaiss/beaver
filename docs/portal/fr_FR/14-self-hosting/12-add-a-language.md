---
id: selfHosting.addLanguage
title: Ajouter une langue
slug: add-a-language
section: self-hosting
---

# Ajouter une langue

KolleK est disponible en sept langues : anglais, français, espagnol, allemand, portugais brésilien, chinois simplifié et japonais. Chaque utilisateur choisit sa propre langue depuis son profil, et peut même la changer depuis la page de connexion. Cette page explique comment fonctionnent les traductions sous le capot, et comment un opérateur ou un contributeur ajoute une nouvelle locale ou en complète une existante.

Si vous souhaitez simplement changer la langue que vous voyez, vous n'avez besoin de rien de tout cela. Voyez @doc(profile.changeLanguage).

## Comment les traductions sont stockées

Chaque locale correspond à un fichier JSON sous `lang/`, nommé d'après le code de la locale, par exemple `lang/fr_FR.json`. Chaque fichier associe la chaîne originale en anglais à sa traduction. La liste des locales que l'application propose est définie dans la configuration de l'application comme les locales prises en charge.

## Amorcer ou actualiser une locale

La commande `beaver:localize` analyse l'ensemble de l'application à la recherche de chaînes traduisibles et les synchronise dans le fichier d'une locale :

```
php artisan beaver:localize fr_FR
```

Les chaînes apparues depuis la dernière exécution sont ajoutées, et les chaînes qui n'existent plus sont retirées. Dans le fichier anglais, chaque chaîne est sa propre traduction, l'anglais est donc toujours complet par définition. Dans chaque autre locale, les nouvelles chaînes arrivent vides, prêtes à être remplies par un traducteur.

Ajouter une toute nouvelle langue suit le même déroulement : enregistrez la locale dans la configuration des locales prises en charge, exécutez la commande avec le nouveau code de locale pour générer son fichier, puis traduisez les entrées vides.

:::note
Une traduction vide se replie sur l'anglais plutôt que de casser l'interface, si bien qu'une locale partiellement traduite reste utilisable pendant que le travail se poursuit.
:::

## Ce qui n'est pas encore traduit

L'application une fois connecté est entièrement traduisible. Le site vitrine public et la référence API générée ne sont pas encore traduits et s'affichent toujours en anglais, quelle que soit la locale du visiteur. Voyez @doc(troubleshooting.featureStatus).

## Et ensuite

- Exécutez la commande sur votre instance avec @doc(selfHosting.cliCommands).
- Découvrez le point de vue du lecteur sur ce sujet dans @doc(profile.changeLanguage).
