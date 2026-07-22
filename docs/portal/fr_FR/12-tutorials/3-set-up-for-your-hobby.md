---
id: tutorials.setupForHobby
title: "Tutoriel : configurer votre compte pour un loisir spécifique"
slug: configurer-pour-un-loisir
section: tutoriels
---

# Tutoriel : configurer votre compte pour un loisir spécifique

Ajouter un objet est facile. En ajouter deux cents n'est facile que si le compte a été préparé au préalable. Dans ce tutoriel, vous allez adapter KolleK à un loisir spécifique avant une saisie de masse : façonner le type de collection et ses champs personnalisés, construire un plan d'emplacements qui reflète votre espace réel, et amorcer un vocabulaire d'étiquettes, afin que chaque objet que vous ajoutez ensuite soit rapide et cohérent.

Nous allons suivre Noah, qui s'apprête à cataloguer environ trois cents vinyles. La même approche fonctionne pour n'importe quel loisir, alors substituez le vôtre au fur et à mesure.

Comptez environ une demi-heure pour ce tutoriel, et cela vous fera gagner de nombreuses heures plus tard.

## Avant de commencer

- Terminez @doc(tutorials.catalogueFirstCollection, "Cataloguer votre première collection de bout en bout") ou au moins le @doc(gettingStarted.quickStart, "démarrage rapide"), afin que la boucle centrale vous soit familière.
- Connaissez les concepts derrière @doc(collectionTypes.overview, "les types de collection et les champs personnalisés"), @doc(locations.overview, "les emplacements"), et @doc(tags.overview, "les étiquettes"). Parcourez ces pages si ce n'est pas le cas.
- Réfléchissez à ce que vous voulez réellement enregistrer pour chaque objet. Dix minutes avec un bloc-notes valent mieux que de retravailler les champs après cinquante entrées.

## Étape 1 : façonner le type de collection

Noah commence avec le type **Vinyles** prêt à l'emploi fourni avec son compte. Il enregistre déjà Ma note, un groupe **Infos de sortie** (Artiste, Album, Année de sortie), et un groupe **Détails de pressage** (Pressage/Édition, Vitesse, Vinyle coloré).

C'est proche de ce qu'il veut, mais il achète beaucoup de pressages japonais et se soucie de l'état des pochettes. Il ajuste donc le type.

::::steps
:::step title="Ouvrir le type"
Allez dans les paramètres des types de collection et sélectionnez **Vinyles**. L'éditeur enregistre au fur et à mesure, il n'y a donc pas de bouton d'enregistrement à chercher.

::screenshot{label="Éditeur de type de collection montrant les champs Vinyles"}
:::

:::step title="Ajouter les champs que vous utiliserez réellement"
Noah ajoute un champ texte **Pays de pressage** au groupe Détails de pressage, et un champ **État de la pochette** en tant que sélection avec les options selon lesquelles il grade. Les types de champ disponibles sont texte, nombre, date, oui ou non, sélection, et notation (jusqu'à cinq étoiles).
:::

:::step title="Regrouper et ordonner les champs"
Créez un nouveau groupe si un ensemble de champs va ensemble, et faites glisser les champs dans l'ordre où vous les voulez sur le formulaire d'objet. Les groupes existent uniquement pour garder les longs formulaires lisibles.
:::
::::

Pourquoi cela compte : les champs personnalisés définis maintenant apparaissent sur chaque formulaire d'objet dans toute collection utilisant ce type. Les décider en amont signifie trois cents fiches cohérentes au lieu de trois cents fiches improvisées.

:::note
Concevez les champs pour les questions que vous poserez plus tard. « Quels disques sont en vinyle coloré » n'a de réponse que si Vinyle coloré est un champ. Un détail enfoui dans une description ne peut pas être scanné.
:::

## Étape 2 : construire votre plan d'emplacements

Noah garde ses disques à deux endroits : une salle d'écoute avec trois étagères, et des caisses en stockage. Il modélise exactement cela, car un emplacement dans KolleK n'est utile que s'il correspond à un endroit où l'on peut physiquement se rendre.

::::steps
:::step title="Créer les emplacements de premier niveau"
Dans @doc(locations.setup, "les paramètres des emplacements"), créez **Salle d'écoute** 🛋️ et **Stockage** 📦. Ce sont les pièces.
:::

:::step title="Imbriquer les subdivisions réelles"
Sous Salle d'écoute, créez **Étagère A**, **Étagère B**, et **Étagère C**. Sous Stockage, créez **Caisse 1** et **Caisse 2**. Les emplacements s'imbriquent aussi profondément que nécessaire, une boîte dans une caisse dans une pièce fonctionne très bien.
:::
::::

Pourquoi cela compte : chaque exemplaire pointe vers un emplacement, et les déplacements ultérieurs sont enregistrés comme @doc(copies.move, "historique d'emplacement"). Un bon plan maintenant signifie que « où se trouve ce disque » a toujours une réponse exacte.

## Étape 3 : amorcer votre vocabulaire d'étiquettes

Les étiquettes traversent les collections et les hiérarchies, ce qui les rend idéales pour les libellés qui ne trouvent leur place nulle part ailleurs. Noah crée son ensemble de départ depuis @doc(tags.manageAccount, "les paramètres des étiquettes") : **Signé**, **Premier pressage**, **Pressage japonais**, **À vendre**, et **À nettoyer**.

Deux habitudes gardent les étiquettes utiles.

- Gardez-les peu nombreuses et réutilisables. Une étiquette utilisée une seule fois est un fait qui aurait dû être un champ ou une note.
- Mettez-vous d'accord sur l'orthographe avant que d'autres ne rejoignent le compte. « Signé » et « Autographié » comme étiquettes séparées vous hanteront.

Vous pouvez toujours créer une étiquette à la volée en modifiant un objet, donc cette liste n'a besoin de couvrir que les libellés que vous savez déjà vouloir.

## Étape 4 : importer un type plutôt que d'en construire un

Il y a un raccourci qui vaut la peine d'être connu. Un type de collection peut être @doc(collectionTypes.importExport, "exporté et importé en JSON"). Si un ami a déjà construit un excellent type Vinyles, il peut l'exporter, et vous pouvez l'importer en collant le JSON, ce qui transfère le nom, la couleur, les groupes, les champs et les options de sélection en une seule étape.

:::note
Importer un type n'apporte que la définition du type. Cela n'importe pas les objets ni leurs données. Il n'existe actuellement aucune importation d'objet ou de collection entière, et l'état honnête de cela est suivi sur la @doc(troubleshooting.featureStatus, "page d'état des fonctionnalités").
:::

Noah importe un type « Singles 45 tours » partagé par un ami de club, et il apparaît à côté de ses propres types, prêt à être rattaché à une collection.

## Étape 5 : créer la collection et tout connecter

Maintenant, les pièces s'assemblent.

::::steps
:::step title="Créer la collection"
Noah crée une collection nommée « Vinyles », choisit l'émoji 💿, et écrit une courte description.
:::

:::step title="Activer les types nécessaires"
Il active à la fois le type **Vinyles** et le type importé **Singles 45 tours**. Une collection peut utiliser plusieurs types, et chaque objet choisit celui qui lui convient.
:::

:::step title="Définir la devise"
Il règle la devise de la collection sur celle dans laquelle il achète réellement ses disques. Elle peut différer de la devise par défaut du compte, et tout l'argent sur les exemplaires de cette collection s'affichera dans celle-ci.
:::
::::

## Le résultat

Ajoutez un disque maintenant et sentez la différence : le formulaire pose exactement les bonnes questions, la liste déroulante des emplacements propose de vraies étagères, et les étiquettes dont vous avez besoin existent déjà. À partir de là, la saisie de masse devient un rythme plutôt qu'une série de décisions.

## Erreurs courantes à éviter

- **Concevoir trop de champs.** Dix champs que vous remplissez valent mieux que vingt-cinq que vous sautez. Vous pouvez ajouter des champs plus tard, les remplir rétroactivement est la partie fastidieuse.
- **Des emplacements qui ne correspondent pas à la réalité.** S'il n'y a pas d'Étagère B physique, l'emplacement « Étagère B » deviendra obsolète immédiatement.
- **Utiliser des étiquettes pour ce que les champs font mieux.** Une note, une année, ou une notation appartient à un champ personnalisé où elle peut être une vraie valeur, pas un libellé.

## Et ensuite

- Commencez à saisir des objets avec @doc(items.addAndEdit).
- Suivez correctement votre pièce la plus précieuse dans @doc(tutorials.trackValuableItem, "Suivre la vie complète d'un objet de valeur").
- Vous travaillez avec d'autres personnes ? @doc(tutorials.inviteHousehold, "Inviter votre foyer ou club").
